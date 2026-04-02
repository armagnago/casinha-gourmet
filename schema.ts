  facebookUrl: varchar("facebookUrl", { length: 500 }),
  stripePublishableKey: varchar("stripePublishableKey", { length: 255 }),
  shippingCost: decimal("shippingCost", { precision: 10, scale: 2 }).default("0"),
  createdAt: timestamp("createdAt").defaultNow().notNull(),
  updatedAt: timestamp("updatedAt").defaultNow().onUpdateNow().notNull(),
});

export type StoreSetting = typeof storeSettings.$inferSelect;
export type InsertStoreSetting = typeof storeSettings.$inferInsert;