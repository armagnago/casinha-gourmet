import { publicProcedure, protectedProcedure, router } from "../_core/trpc";
import { z } from "zod";
import Stripe from "stripe";
import { ENV } from "../_core/env";

const stripe = new Stripe(process.env.STRIPE_SECRET_KEY || "");

export const paymentsRouter = router({
  // Create checkout session for order
  createCheckoutSession: publicProcedure
    .input(
      z.object({
        orderId: z.number(),
        orderNumber: z.string(),
        total: z.string(),
        customerEmail: z.string().email(),
        customerName: z.string(),
        items: z.array(
          z.object({
            name: z.string(),
            quantity: z.number(),
            price: z.string(),
          })
        ),
      })
    )
    .mutation(async ({ input, ctx }) => {
      try {
        const lineItems = input.items.map((item) => ({
          price_data: {
            currency: "brl",
            product_data: {
              name: item.name,
            },
            unit_amount: Math.round(parseFloat(item.price) * 100), // Convert to cents
          },
          quantity: item.quantity,
        }));

        const session = await stripe.checkout.sessions.create({
          payment_method_types: ["card"] as any,
          line_items: lineItems,
          mode: "payment" as any,
          success_url: `${ctx.req.headers.origin}/pedido-confirmado?session_id={CHECKOUT_SESSION_ID}`,
          cancel_url: `${ctx.req.headers.origin}/carrinho`,
          customer_email: input.customerEmail,
          metadata: {
            order_id: input.orderId.toString(),
            order_number: input.orderNumber,
            customer_name: input.customerName,
            customer_email: input.customerEmail,
          },
        });

        return {
          sessionId: session.id,
          checkoutUrl: session.url,
        };
      } catch (error) {
        console.error("Stripe checkout error:", error);
        throw new Error("Erro ao criar sessão de checkout");
      }
    }),

  // Get checkout session status
  getCheckoutSession: publicProcedure
    .input(z.object({ sessionId: z.string() }))
    .query(async ({ input }) => {
      try {
        const session = await stripe.checkout.sessions.retrieve(input.sessionId) as any;
        return {
          paymentStatus: session.payment_status,
          status: session.status,
          customerId: session.customer,
        };
      } catch (error) {
        console.error("Stripe session retrieval error:", error);
        throw new Error("Erro ao recuperar sessão de checkout");
      }
    }),

  // Retrieve payment intent
  getPaymentIntent: publicProcedure
    .input(z.object({ paymentIntentId: z.string() }))
    .query(async ({ input }) => {
      try {
        const paymentIntent = await stripe.paymentIntents.retrieve(
          input.paymentIntentId
        ) as any;
        return {
          status: paymentIntent.status,
          amount: paymentIntent.amount,
          currency: paymentIntent.currency,
        };
      } catch (error) {
        console.error("Stripe payment intent error:", error);
        throw new Error("Erro ao recuperar intenção de pagamento");
      }
    }),
});
