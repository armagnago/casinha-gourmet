import { useState } from "react";
import { useAuth } from "@/_core/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Link } from "wouter";
import { Plus, Edit2, Trash2, Eye } from "lucide-react";
import { trpc } from "@/lib/trpc";
import { toast } from "sonner";

export default function Admin() {
  const { user, isAuthenticated } = useAuth();
  const [activeTab, setActiveTab] = useState<"products" | "orders">("products");
  const [showProductForm, setShowProductForm] = useState(false);
  const [editingProduct, setEditingProduct] = useState<any>(null);
  const [formData, setFormData] = useState({
    categoryId: 1,
    name: "",
    slug: "",
    description: "",
    price: "",
    minQuantity: 1,
  });

  // Queries
  const { data: categories = [] } = trpc.categories.list.useQuery();
  const { data: products = [] } = trpc.products.list.useQuery();
  const { data: orders = [] } = trpc.orders.list.useQuery();

  // Mutations
  const createProductMutation = trpc.products.create.useMutation();
  const updateProductMutation = trpc.products.update.useMutation();
  const deleteProductMutation = trpc.products.delete.useMutation();
  const updateOrderStatusMutation = trpc.orders.updateStatus.useMutation();

  // Check admin access
  if (!isAuthenticated || user?.role !== "admin") {
    return (
      <div className="min-h-screen bg-cream flex items-center justify-center">
        <div className="text-center">
          <h1 className="font-script text-4xl text-charcoal mb-4">Acesso Negado</h1>
          <p className="font-mono text-charcoal mb-6">
            Você precisa ser um administrador para acessar esta página
          </p>
          <Link href="/">
            <Button className="bg-warm-orange hover:bg-chocolate text-cream font-mono border-2 border-charcoal">
              Voltar para Home
            </Button>
          </Link>
        </div>
      </div>
    );
  }

  const handleSaveProduct = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      if (editingProduct) {
        await updateProductMutation.mutateAsync({
          id: editingProduct.id,
          data: formData,
        });
        toast.success("Produto atualizado com sucesso!");
      } else {
        await createProductMutation.mutateAsync(formData);
        toast.success("Produto criado com sucesso!");
      }

      setShowProductForm(false);
      setEditingProduct(null);
      setFormData({
        categoryId: 1,
        name: "",
        slug: "",
        description: "",
        price: "",
        minQuantity: 1,
      });
    } catch (error: any) {
      toast.error(error.message || "Erro ao salvar produto");
    }
  };

  const handleDeleteProduct = async (id: number) => {
    if (!confirm("Tem certeza que deseja deletar este produto?")) return;

    try {
      await deleteProductMutation.mutateAsync({ id });
      toast.success("Produto deletado com sucesso!");
    } catch (error: any) {
      toast.error(error.message || "Erro ao deletar produto");
    }
  };

  const handleUpdateOrderStatus = async (
    orderId: number,
    newStatus: string
  ) => {
    try {
      await updateOrderStatusMutation.mutateAsync({
        id: orderId,
        status: newStatus as any,
      });
      toast.success("Status do pedido atualizado!");
    } catch (error: any) {
      toast.error(error.message || "Erro ao atualizar status");
    }
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
          <div className="font-mono text-charcoal">
            Admin: {user?.name}
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="container mx-auto px-4 py-12">
        <h2 className="font-script text-4xl text-charcoal mb-8">Painel Administrativo</h2>

        {/* Tabs */}
        <div className="flex gap-4 mb-8 border-b-2 border-charcoal pb-4">
          <button
            onClick={() => setActiveTab("products")}
            className={`font-mono font-bold px-4 py-2 border-b-4 ${
              activeTab === "products"
                ? "border-warm-orange text-warm-orange"
                : "border-transparent text-charcoal hover:text-chocolate"
            }`}
          >
            Produtos
          </button>
          <button
            onClick={() => setActiveTab("orders")}
            className={`font-mono font-bold px-4 py-2 border-b-4 ${
              activeTab === "orders"
                ? "border-warm-orange text-warm-orange"
                : "border-transparent text-charcoal hover:text-chocolate"
            }`}
          >
            Encomendas
          </button>
        </div>

        {/* Products Tab */}
        {activeTab === "products" && (
          <div>
            <div className="mb-6">
              {!showProductForm ? (
                <Button
                  onClick={() => setShowProductForm(true)}
                  className="bg-warm-orange hover:bg-chocolate text-cream font-mono border-2 border-charcoal"
                >
                  <Plus className="w-4 h-4 mr-2" />
                  Novo Produto
                </Button>
              ) : (
                <div className="bg-beige border-2 border-charcoal rounded-lg p-6 mb-6">
                  <h3 className="font-script text-2xl text-charcoal mb-4">
                    {editingProduct ? "Editar Produto" : "Novo Produto"}
                  </h3>

                  <form onSubmit={handleSaveProduct} className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label className="block font-mono text-charcoal text-sm mb-1">
                          Categoria
                        </label>
                        <select
                          value={formData.categoryId}
                          onChange={(e) =>
                            setFormData({
                              ...formData,
                              categoryId: parseInt(e.target.value),
                            })
                          }
                          className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                        >
                          {categories.map((cat: any) => (
                            <option key={cat.id} value={cat.id}>
                              {cat.name}
                            </option>
                          ))}
                        </select>
                      </div>

                      <div>
                        <label className="block font-mono text-charcoal text-sm mb-1">
                          Nome
                        </label>
                        <input
                          type="text"
                          value={formData.name}
                          onChange={(e) =>
                            setFormData({ ...formData, name: e.target.value })
                          }
                          className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                          required
                        />
                      </div>

                      <div>
                        <label className="block font-mono text-charcoal text-sm mb-1">
                          Slug
                        </label>
                        <input
                          type="text"
                          value={formData.slug}
                          onChange={(e) =>
                            setFormData({ ...formData, slug: e.target.value })
                          }
                          className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                          required
                        />
                      </div>

                      <div>
                        <label className="block font-mono text-charcoal text-sm mb-1">
                          Preço (R$)
                        </label>
                        <input
                          type="number"
                          step="0.01"
                          value={formData.price}
                          onChange={(e) =>
                            setFormData({ ...formData, price: e.target.value })
                          }
                          className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                          required
                        />
                      </div>

                      <div>
                        <label className="block font-mono text-charcoal text-sm mb-1">
                          Quantidade Mínima
                        </label>
                        <input
                          type="number"
                          value={formData.minQuantity}
                          onChange={(e) =>
                            setFormData({
                              ...formData,
                              minQuantity: parseInt(e.target.value),
                            })
                          }
                          className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                        />
                      </div>
                    </div>

                    <div>
                      <label className="block font-mono text-charcoal text-sm mb-1">
                        Descrição
                      </label>
                      <textarea
                        value={formData.description}
                        onChange={(e) =>
                          setFormData({
                            ...formData,
                            description: e.target.value,
                          })
                        }
                        className="w-full border-2 border-charcoal rounded px-3 py-2 font-mono"
                        rows={3}
                      />
                    </div>

                    <div className="flex gap-4">
                      <Button
                        type="submit"
                        disabled={createProductMutation.isPending || updateProductMutation.isPending}
                        className="bg-warm-orange hover:bg-chocolate text-cream font-mono border-2 border-charcoal"
                      >
                        {createProductMutation.isPending || updateProductMutation.isPending
                          ? "Salvando..."
                          : "Salvar"}
                      </Button>
                      <Button
                        type="button"
                        onClick={() => {
                          setShowProductForm(false);
                          setEditingProduct(null);
                          setFormData({
                            categoryId: 1,
                            name: "",
                            slug: "",
                            description: "",
                            price: "",
                            minQuantity: 1,
                          });
                        }}
                        variant="outline"
                        className="border-2 border-charcoal text-charcoal font-mono"
                      >
                        Cancelar
                      </Button>
                    </div>
                  </form>
                </div>
              )}
            </div>

            {/* Products List */}
            <div className="space-y-4">
              {products.map((product: any) => (
                <div
                  key={product.id}
                  className="bg-white border-2 border-charcoal rounded-lg p-4 flex justify-between items-center"
                >
                  <div>
                    <h4 className="font-script text-xl text-charcoal">
                      {product.name}
                    </h4>
                    <p className="font-mono text-charcoal text-sm">
                      R$ {parseFloat(product.price).toFixed(2)}
                    </p>
                  </div>

                  <div className="flex gap-2">
                    <button
                      onClick={() => {
                        setEditingProduct(product);
                        setFormData({
                          categoryId: product.categoryId,
                          name: product.name,
                          slug: product.slug,
                          description: product.description || "",
                          price: product.price,
                          minQuantity: product.minQuantity || 1,
                        });
                        setShowProductForm(true);
                      }}
                      className="text-chocolate hover:text-warm-orange"
                    >
                      <Edit2 className="w-5 h-5" />
                    </button>
                    <button
                      onClick={() => handleDeleteProduct(product.id)}
                      className="text-warm-orange hover:text-chocolate"
                    >
                      <Trash2 className="w-5 h-5" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Orders Tab */}
        {activeTab === "orders" && (
          <div className="space-y-4">
            {orders.map((order: any) => (
              <div
                key={order.id}
                className="bg-white border-2 border-charcoal rounded-lg p-4"
              >
                <div className="flex justify-between items-start mb-4">
                  <div>
                    <h4 className="font-script text-xl text-charcoal">
                      Pedido #{order.orderNumber}
                    </h4>
                    <p className="font-mono text-charcoal text-sm">
                      Cliente: {order.customerName}
                    </p>
                    <p className="font-mono text-charcoal text-sm">
                      Email: {order.customerEmail}
                    </p>
                    <p className="font-mono text-charcoal text-sm">
                      WhatsApp: {order.customerPhone}
                    </p>
                  </div>

                  <div className="text-right">
                    <p className="font-mono text-charcoal font-bold">
                      Total: R$ {parseFloat(order.total).toFixed(2)}
                    </p>
                    <p className="font-mono text-charcoal text-sm">
                      Status: {order.status}
                    </p>
                  </div>
                </div>

                {/* Status Update */}
                <div className="flex gap-2 flex-wrap">
                  {["pending", "confirmed", "preparing", "ready", "delivered"].map(
                    (status) => (
                      <button
                        key={status}
                        onClick={() => handleUpdateOrderStatus(order.id, status)}
                        className={`font-mono text-xs px-3 py-1 rounded border-2 border-charcoal ${
                          order.status === status
                            ? "bg-warm-orange text-cream"
                            : "bg-cream text-charcoal hover:bg-beige"
                        }`}
                      >
                        {status}
                      </button>
                    )
                  )}
                </div>
              </div>
            ))}
          </div>
        )}
      </main>
    </div>
  );
}
