import { useState, useEffect } from "react";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { Trash2, ArrowLeft } from "lucide-react";
import { trpc } from "@/lib/trpc";
import { toast } from "sonner";

export default function Cart() {
  const [cartItems, setCartItems] = useState<any[]>([]);
  const [formData, setFormData] = useState({
    customerName: "",
    customerEmail: "",
    customerPhone: "",
    customerAddress: "",
    deliveryDate: "",
    specialInstructions: "",
  });

  const createOrderMutation = trpc.orders.create.useMutation();

  // Load cart from localStorage
  useEffect(() => {
    const savedCart = localStorage.getItem("cart");
    if (savedCart) {
      setCartItems(JSON.parse(savedCart));
    }
  }, []);

  const removeFromCart = (productId: number) => {
    const updatedCart = cartItems.filter((item) => item.id !== productId);
    setCartItems(updatedCart);
    localStorage.setItem("cart", JSON.stringify(updatedCart));
  };

  const updateQuantity = (productId: number, quantity: number) => {
    if (quantity <= 0) {
      removeFromCart(productId);
      return;
    }
    const updatedCart = cartItems.map((item) =>
      item.id === productId ? { ...item, quantity } : item
    );
    setCartItems(updatedCart);
    localStorage.setItem("cart", JSON.stringify(updatedCart));
  };

  const calculateTotal = () => {
    return cartItems.reduce(
      (total, item) => total + parseFloat(item.price) * item.quantity,
      0
    );
  };

  const handleSubmitOrder = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!formData.customerName || !formData.customerEmail || !formData.customerPhone) {
      toast.error("Preencha todos os campos obrigatórios");
      return;
    }

    if (cartItems.length === 0) {
      toast.error("Seu carrinho está vazio");
      return;
    }

    try {
      const result = await createOrderMutation.mutateAsync({
        customerName: formData.customerName,
        customerEmail: formData.customerEmail,
        customerPhone: formData.customerPhone,
        customerAddress: formData.customerAddress,
        deliveryDate: new Date(formData.deliveryDate),
        specialInstructions: formData.specialInstructions,
        items: cartItems.map((item) => ({
          productId: item.id,
          quantity: item.quantity,
        })),
      });

      toast.success(`Pedido criado com sucesso! Número: ${result.orderNumber}`);
      setCartItems([]);
      localStorage.removeItem("cart");
      setFormData({
        customerName: "",
        customerEmail: "",
        customerPhone: "",
        customerAddress: "",
        deliveryDate: "",
        specialInstructions: "",
      });

      // Redirect to order confirmation or home
      setTimeout(() => {
        window.location.href = "/";
      }, 2000);
    } catch (error: any) {
      toast.error(error.message || "Erro ao criar pedido");
    }
  };

  if (cartItems.length === 0) {
    return (
      <div className="min-h-screen bg-cream">
        {/* Header */}
        <header className="border-b-2 border-charcoal bg-cream sticky top-0 z-50">
          <div className="container mx-auto px-4 py-4">
            <Link href="/">
              <h1 className="font-script text-3xl text-charcoal cursor-pointer">
                Doces & Salgados
              </h1>
            </Link>
          </div>
        </header>

        {/* Empty Cart */}
        <main className="container mx-auto px-4 py-12 text-center">
          <h2 className="font-script text-4xl text-charcoal mb-4">Seu Carrinho</h2>
          <p className="font-mono text-charcoal text-lg mb-8">
            Seu carrinho está vazio
          </p>
          <Link href="/catalogo">
            <Button className="bg-warm-orange hover:bg-chocolate text-cream font-mono border-2 border-charcoal">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Voltar ao Catálogo
            </Button>
          </Link>
        </main>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-cream">
      {/* Header */}
      <header className="border-b-2 border-charcoal bg-cream sticky top-0 z-50">
        <div className="container mx-auto px-4 py-4">
          <Link href="/">
            <h1 className="font-script text-3xl text-charcoal cursor-pointer">
              Doces & Salgados
            </h1>
          </Link>
        </div>
      </header>

      {/* Main Content */}
      <main className="container mx-auto px-4 py-12">
        <h2 className="font-script text-4xl text-charcoal mb-8">Seu Carrinho</h2>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Cart Items */}
          <div className="lg:col-span-2">
            <div className="space-y-4">
              {cartItems.map((item) => (
                <div
                  key={item.id}
                  className="bg-white border-2 border-charcoal rounded-lg p-4 flex justify-between items-center"
                >
                  <div className="flex-1">
                    <h4 className="font-script text-xl text-charcoal">
                      {item.name}
                    </h4>
                    <p className="font-mono text-charcoal text-sm">
                      R$ {parseFloat(item.price).toFixed(2)} cada
                    </p>
                  </div>

                  <div className="flex items-center gap-4">
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => updateQuantity(item.id, item.quantity - 1)}
                        className="border-2 border-charcoal px-2 py-1 font-mono text-charcoal hover:bg-beige"
                      >
                        -
                      </button>
                      <span className="font-mono text-charcoal w-8 text-center">
                        {item.quantity}
                      </span>
                      <button
                        onClick={() => updateQuantity(item.id, item.quantity + 1)}
                        className="border-2 border-charcoal px-2 py-1 font-mono text-charcoal hover:bg-beige"
                      >
                        +
                      </button>
                    </div>

                    <span className="font-mono text-charcoal font-bold w-24 text-right">
                      R$ {(parseFloat(item.price) * item.quantity).toFixed(2)}
                    </span>

                    <button
                      onClick={() => removeFromCart(item.id)}
                      className="text-warm-orange hover:text-chocolate"
                    >
                      <Trash2 className="w-5 h-5" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Order Form */}
          <div className="lg:col-span-1">
            <div className="bg-beige border-2 border-charcoal rounded-lg p-6">
              <h3 className="font-script text-2xl text-charcoal mb-4">
                Resumo do Pedido
              </h3>

              {/* Total */}
              <div className="mb-6 pb-6 border-b-2 border-charcoal">
                <div className="flex justify-between items-center mb-2">
                  <span className="font-mono text-charcoal">Subtotal:</span>
                  <span className="font-mono text-charcoal font-bold">
                    R$ {calculateTotal().toFixed(2)}
                  </span>
                </div>
                <div className="flex justify-between items-center text-lg">
                  <span className="font-script text-charcoal">Total:</span>
                  <span className="font-mono text-warm-orange font-bold text-2xl">
                    R$ {calculateTotal().toFixed(2)}
                  </span>
                </div>
              </div>

              {/* Form */}
              <form onSubmit={handleSubmitOrder} className="space-y-4">
                <div>
                  <label className="block font-mono text-charcoal text-sm mb-1">
                    Nome *
                  </label>
                  <input
                    type="text"
                    value={formData.customerName}
                    onChange={(e) =>
                      setFormData({ ...formData, customerName: e.target.value })
                    }
                    className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                    required
                  />
                </div>

                <div>
                  <label className="block font-mono text-charcoal text-sm mb-1">
                    Email *
                  </label>
                  <input
                    type="email"
                    value={formData.customerEmail}
                    onChange={(e) =>
                      setFormData({ ...formData, customerEmail: e.target.value })
                    }
                    className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                    required
                  />
                </div>

                <div>
                  <label className="block font-mono text-charcoal text-sm mb-1">
                    WhatsApp *
                  </label>
                  <input
                    type="tel"
                    value={formData.customerPhone}
                    onChange={(e) =>
                      setFormData({ ...formData, customerPhone: e.target.value })
                    }
                    className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                    placeholder="(11) 99999-9999"
                    required
                  />
                </div>

                <div>
                  <label className="block font-mono text-charcoal text-sm mb-1">
                    Endereço
                  </label>
                  <input
                    type="text"
                    value={formData.customerAddress}
                    onChange={(e) =>
                      setFormData({ ...formData, customerAddress: e.target.value })
                    }
                    className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                  />
                </div>

                <div>
                  <label className="block font-mono text-charcoal text-sm mb-1">
                    Data de Entrega *
                  </label>
                  <input
                    type="date"
                    value={formData.deliveryDate}
                    onChange={(e) =>
                      setFormData({ ...formData, deliveryDate: e.target.value })
                    }
                    className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                    required
                  />
                </div>

                <div>
                  <label className="block font-mono text-charcoal text-sm mb-1">
                    Observações
                  </label>
                  <textarea
                    value={formData.specialInstructions}
                    onChange={(e) =>
                      setFormData({
                        ...formData,
                        specialInstructions: e.target.value,
                      })
                    }
                    className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                    rows={3}
                  />
                </div>

                <Button
                  type="submit"
                  disabled={createOrderMutation.isPending}
                  className="w-full bg-warm-orange hover:bg-chocolate text-cream font-mono border-2 border-charcoal"
                >
                  {createOrderMutation.isPending ? "Processando..." : "Confirmar Pedido"}
                </Button>
              </form>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
