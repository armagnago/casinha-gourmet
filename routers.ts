import { COOKIE_NAME } from "@shared/const";
import { getSessionCookieOptions } from "./_core/cookies";
import { systemRouter } from "./_core/systemRouter";
import { publicProcedure, protectedProcedure, router } from "./_core/trpc";
import { z } from "zod";
import * as db from "./db";
import { TRPCError } from "@trpc/server";
import { paymentsRouter } from "./routers/payments";

// ============ VALIDATION SCHEMAS ============

const ProductSchema = z.object({
  categoryId: z.number(),
  name: z.string().min(1),
  slug: z.string().min(1),
  description: z.string().optional(),
  longDescription: z.string().optional(),
  price: z.string(),
  image: z.string().optional(),
  images: z.any().optional(),
  minQuantity: z.number().optional().default(1),
  maxQuantity: z.number().optional(),
  available: z.boolean().optional().default(true),
  displayOrder: z.number().optional(),
});

const OrderSchema = z.object({
  customerName: z.string().min(1),
  customerEmail: z.string().email(),
  customerPhone: z.string().min(1),
  customerAddress: z.string().optional(),
  deliveryDate: z.date(),
  specialInstructions: z.string().optional(),
  items: z.array(z.object({
    productId: z.number(),
    quantity: z.number().min(1),
  })),
});

// ============ ROUTERS ============

export const appRouter = router({
  system: systemRouter,
  
  auth: router({
    me: publicProcedure.query(opts => opts.ctx.user),
    logout: publicProcedure.mutation(({ ctx }) => {
      const cookieOptions = getSessionCookieOptions(ctx.req);
      ctx.res.clearCookie(COOKIE_NAME, { ...cookieOptions, maxAge: -1 });
      return {
        success: true,
      } as const;
    }),
  }),

  // ============ CATEGORIES ============
  
  categories: router({
    list: publicProcedure.query(async () => {
      return await db.getCategories();
    }),

    getBySlug: publicProcedure
      .input(z.object({ slug: z.string() }))
      .query(async ({ input }) => {
        return await db.getCategoryBySlug(input.slug);
      }),

    create: protectedProcedure
      .input(z.object({
        name: z.string(),
        slug: z.string(),
        description: z.string().optional(),
        icon: z.string().optional(),
        displayOrder: z.number().optional(),
      }))
      .mutation(async ({ ctx, input }) => {
        if (ctx.user.role !== 'admin') {
          throw new TRPCError({ code: 'FORBIDDEN' });
        }
        await db.createCategory(input);
        return { success: true };
      }),
  }),

  // ============ PRODUCTS ============
  
  products: router({
    list: publicProcedure
      .input(z.object({
        categoryId: z.number().optional(),
        available: z.boolean().optional(),
      }).optional())
      .query(async ({ input }) => {
        return await db.getProducts(input);
      }),

    getById: publicProcedure
      .input(z.object({ id: z.number() }))
      .query(async ({ input }) => {
        return await db.getProductById(input.id);
      }),

    getBySlug: publicProcedure
      .input(z.object({ slug: z.string() }))
      .query(async ({ input }) => {
        return await db.getProductBySlug(input.slug);
      }),

    create: protectedProcedure
      .input(ProductSchema)
      .mutation(async ({ ctx, input }) => {
        if (ctx.user.role !== 'admin') {
          throw new TRPCError({ code: 'FORBIDDEN' });
        }
        await db.createProduct(input);
        return { success: true };
      }),

    update: protectedProcedure
      .input(z.object({
        id: z.number(),
        data: ProductSchema.partial(),
      }))
      .mutation(async ({ ctx, input }) => {
        if (ctx.user.role !== 'admin') {
          throw new TRPCError({ code: 'FORBIDDEN' });
        }
        await db.updateProduct(input.id, input.data);
        return { success: true };
      }),

    delete: protectedProcedure
      .input(z.object({ id: z.number() }))
      .mutation(async ({ ctx, input }) => {
        if (ctx.user.role !== 'admin') {
          throw new TRPCError({ code: 'FORBIDDEN' });
        }
        await db.deleteProduct(input.id);
        return { success: true };
      }),
  }),

  // ============ ORDERS ============
  
  orders: router({
    list: protectedProcedure
      .input(z.object({
        status: z.string().optional(),
        limit: z.number().optional(),
        offset: z.number().optional(),
      }).optional())
      .query(async ({ ctx, input }) => {
        if (ctx.user.role !== 'admin') {
          throw new TRPCError({ code: 'FORBIDDEN' });
        }
        return await db.getOrders(input);
      }),

    getById: protectedProcedure
      .input(z.object({ id: z.number() }))
      .query(async ({ ctx, input }) => {
        if (ctx.user.role !== 'admin') {
          throw new TRPCError({ code: 'FORBIDDEN' });
        }
        const order = await db.getOrderById(input.id);
        if (!order) {
          throw new TRPCError({ code: 'NOT_FOUND' });
        }
        return order;
      }),

    create: publicProcedure
      .input(OrderSchema)
      .mutation(async ({ input }) => {
        // Calculate totals
        let subtotal = 0;
        const orderItems: any[] = [];

        for (const item of input.items) {
          const product = await db.getProductById(item.productId);
          if (!product) {
            throw new TRPCError({ 
              code: 'NOT_FOUND',
              message: `Produto ${item.productId} não encontrado`,
            });
          }

          const itemSubtotal = parseFloat(product.price) * item.quantity;
          subtotal += itemSubtotal;

          orderItems.push({
            productId: item.productId,
            productName: product.name,
            quantity: item.quantity,
            unitPrice: product.price,
            subtotal: itemSubtotal.toFixed(2),
          });
        }

        const shippingCost = 0; // TODO: Calculate based on address
        const total = (subtotal + shippingCost).toFixed(2);
        const orderNumber = await db.generateOrderNumber();

        const result = await db.createOrder({
          orderNumber,
          customerName: input.customerName,
          customerEmail: input.customerEmail,
          customerPhone: input.customerPhone,
          customerAddress: input.customerAddress,
          deliveryDate: input.deliveryDate,
          specialInstructions: input.specialInstructions,
          subtotal: subtotal.toFixed(2),
          shippingCost: shippingCost.toFixed(2),
          total,
          paymentMethod: 'pending',
          paymentStatus: 'pending',
        });

        // Get the created order ID (assuming result has insertId or similar)
        // For now, we'll fetch it by orderNumber
        const createdOrder = await db.getOrderByNumber(orderNumber);
        if (!createdOrder) {
          throw new TRPCError({ code: 'INTERNAL_SERVER_ERROR' });
        }

        // Create order items
        for (const item of orderItems) {
          await db.createOrderItem({
            orderId: createdOrder.id,
            ...item,
          });
        }

        // TODO: Send notification email to owner
        // TODO: Send confirmation email to customer

        return {
          success: true,
          orderNumber,
          orderId: createdOrder.id,
          total,
        };
      }),

    updateStatus: protectedProcedure
      .input(z.object({
        id: z.number(),
        status: z.enum(['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled']),
        paymentStatus: z.enum(['pending', 'completed', 'failed', 'refunded']).optional(),
      }))
      .mutation(async ({ ctx, input }) => {
        if (ctx.user.role !== 'admin') {
          throw new TRPCError({ code: 'FORBIDDEN' });
        }
        await db.updateOrderStatus(input.id, input.status, input.paymentStatus);
        return { success: true };
      }),

    getItems: publicProcedure
      .input(z.object({ orderId: z.number() }))
      .query(async ({ input }) => {
        return await db.getOrderItems(input.orderId);
      }),
  }),

  // ============ STORE SETTINGS ============
  
  payments: paymentsRouter,

  storeSettings: router({
    get: publicProcedure.query(async () => {
      return await db.getStoreSettings();
    }),

    update: protectedProcedure
      .input(z.object({
        storeName: z.string().optional(),
        storeDescription: z.string().optional(),
        ownerEmail: z.string().optional(),
        ownerPhone: z.string().optional(),
        whatsappNumber: z.string().optional(),
        instagramUrl: z.string().optional(),
        facebookUrl: z.string().optional(),
        stripePublishableKey: z.string().optional(),
        shippingCost: z.string().optional(),
      }))
      .mutation(async ({ ctx, input }) => {
        if (ctx.user.role !== 'admin') {
          throw new TRPCError({ code: 'FORBIDDEN' });
        }
        await db.updateStoreSettings(input);
        return { success: true };
      }),
  }),
});

export type AppRouter = typeof appRouter;

// Re-export payments router type
export type PaymentsRouter = typeof paymentsRouter;
