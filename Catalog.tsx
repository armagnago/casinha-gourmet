import { useState, useEffect } from "react";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { ShoppingCart } from "lucide-react";
import { trpc } from "@/lib/trpc";

export default function Catalog() {
  const [selectedCategory, setSelectedCategory] = useState<number | undefined>();
  const [cart, setCart] = useState<any[]>([]);

  // Fetch categories
  const { data: categories = [] } = trpc.categories.list.useQuery();

  // Fetch products
  const { data: products = [] } = trpc.products.list.useQuery({
    categoryId: selectedCategory,
    available: true,
  });

  const addToCart = (product: any) => {
    const existingItem = cart.find((item) => item.id === product.id);
    if (existingItem) {
      setCart(
        cart.map((item) =>
          item.id === product.id
            ? { ...item, quantity: item.quantity + 1 }
            : item
        )
      );
    } else {
      setCart([...cart, { ...product, quantity: 1 }]);
    }
    // Save to localStorage
    localStorage.setItem("cart", JSON.stringify([...cart, { ...product, quantity: 1 }]));
  };

  return (
    <div className="min-h-screen bg-cream">
      {/* Header */}
      <header className="border-b-2 border-charcoal bg-cream sticky top-0 z-50">
        <div className="container mx-auto px-4 py-4 flex justify-between items-center">
          <Link href="/">
            <h1 className="font-script text-3xl text-charcoal cursor-pointer">
              Doces & Salgados
            </h1>
          </Link>
          <Link href="/carrinho" className="relative">
            <div className="relative">
              <ShoppingCart className="w-6 h-6 text-charcoal hover:text-chocolate" />
              {cart.length > 0 && (
                <span className="absolute -top-2 -right-2 bg-warm-orange text-cream text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                  {cart.length}
                </span>
              )}
            </div>
          </Link>
        </div>
      </header>

      {/* Main Content */}
      <main className="container mx-auto px-4 py-12">
        <h2 className="font-script text-4xl text-charcoal mb-8">Nosso Catálogo</h2>

        {/* Category Filter */}
        <div className="mb-12">
          <h3 className="font-script text-2xl text-charcoal mb-4">Categorias</h3>
          <div className="flex flex-wrap gap-3">
            <Button
              onClick={() => setSelectedCategory(undefined)}
              variant={selectedCategory === undefined ? "default" : "outline"}
              className={`font-mono border-2 border-charcoal ${
                selectedCategory === undefined
                  ? "bg-warm-orange text-cream"
                  : "bg-cream text-charcoal hover:bg-beige"
              }`}
            >
              Todos
            </Button>
            {categories.map((category: any) => (
              <Button
                key={category.id}
                onClick={() => setSelectedCategory(category.id)}
                variant={selectedCategory === category.id ? "default" : "outline"}
                className={`font-mono border-2 border-charcoal ${
                  selectedCategory === category.id
                    ? "bg-warm-orange text-cream"
                    : "bg-cream text-charcoal hover:bg-beige"
                }`}
              >
                {category.name}
              </Button>
            ))}
          </div>
        </div>

        {/* Products Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {products.length > 0 ? (
            products.map((product: any) => (
              <div
                key={product.id}
                className="bg-cream border-2 border-charcoal rounded-lg overflow-hidden hover:shadow-lg transition"
              >
                {/* Product Image */}
                {product.image && (
                  <img
                    src={product.image}
                    alt={product.name}
                    className="w-full h-48 object-cover"
                  />
                )}

                {/* Product Info */}
                <div className="p-6">
                  <h4 className="font-script text-2xl text-charcoal mb-2">
                    {product.name}
                  </h4>
                  <p className="font-mono text-charcoal text-sm mb-4">
                    {product.description}
                  </p>

                  {/* Price */}
                  <div className="flex justify-between items-center mb-4">
                    <span className="font-mono text-2xl text-warm-orange font-bold">
                      R$ {parseFloat(product.price).toFixed(2)}
                    </span>
                  </div>

                  {/* Quantity Info */}
                  {product.minQuantity && (
                    <p className="font-mono text-xs text-charcoal mb-4">
                      Mínimo: {product.minQuantity} unidades
                    </p>
                  )}

                  {/* Add to Cart Button */}
                  <Button
                    onClick={() => addToCart(product)}
                    className="w-full bg-warm-orange hover:bg-chocolate text-cream font-mono border-2 border-charcoal"
                  >
                    <ShoppingCart className="w-4 h-4 mr-2" />
                    Adicionar ao Carrinho
                  </Button>
                </div>
              </div>
            ))
          ) : (
            <div className="col-span-full text-center py-12">
              <p className="font-mono text-charcoal text-lg">
                Nenhum produto encontrado nesta categoria.
              </p>
            </div>
          )}
        </div>
      </main>

      {/* Footer */}
      <footer className="bg-charcoal text-cream border-t-2 border-charcoal py-8 mt-16">
        <div className="container mx-auto px-4 text-center font-mono text-sm">
          <p>&copy; 2026 Doces & Salgados. Todos os direitos reservados.</p>
        </div>
      </footer>
    </div>
  );
}
