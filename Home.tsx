import { useAuth } from "@/_core/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Link } from "wouter";
import { ShoppingCart, Instagram, Facebook, MessageCircle } from "lucide-react";

export default function Home() {
  const { user, isAuthenticated } = useAuth();

  return (
    <div className="min-h-screen bg-cream">
      {/* Header/Navigation */}
      <header className="border-b-2 border-charcoal bg-cream sticky top-0 z-50">
        <div className="container mx-auto px-4 py-4 flex justify-between items-center">
          <div className="flex items-center gap-2">
            <h1 className="font-script text-3xl text-charcoal">Doces & Salgados</h1>
          </div>
          <nav className="flex items-center gap-6">
            <Link href="/catalogo" className="text-charcoal hover:text-chocolate font-mono text-sm">
              Catálogo
            </Link>
            <Link href="/sobre" className="text-charcoal hover:text-chocolate font-mono text-sm">
              Sobre
            </Link>
            <Link href="/contato" className="text-charcoal hover:text-chocolate font-mono text-sm">
              Contato
            </Link>
            {isAuthenticated && (
              <Link href="/admin" className="text-charcoal hover:text-chocolate font-mono text-sm">
                Admin
              </Link>
            )}
            <Link href="/carrinho" className="relative">
              <ShoppingCart className="w-6 h-6 text-charcoal hover:text-chocolate" />
            </Link>
          </nav>
        </div>
      </header>

      {/* Hero Section */}
      <section className="relative py-16 overflow-hidden">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            {/* Hero Image */}
            <div className="flex justify-center">
              <img
                src="https://d2xsxph8kpxj0f.cloudfront.net/310519663406919948/8c4HmBwzgWyfgPvnwRXdCh/hero-sketch-illustration-G2jo2apnTJSfrU2LuZvaXL.webp"
                alt="Loja de Doces e Salgados"
                className="w-full max-w-md"
              />
            </div>

            {/* Hero Text */}
            <div className="space-y-6">
              <div>
                <h2 className="font-script text-5xl text-charcoal mb-4">
                  Bem-vindo à Nossa Confeitaria!
                </h2>
                <p className="font-mono text-charcoal text-lg leading-relaxed">
                  Somos uma confeitaria artesanal dedicada a criar doces e salgados feitos com amor e ingredientes de qualidade. Cada produto é preparado com atenção aos detalhes para garantir o melhor sabor e apresentação.
                </p>
              </div>

              <div className="flex gap-4">
                <Link href="/catalogo">
                  <Button className="bg-warm-orange hover:bg-chocolate text-cream font-mono border-2 border-charcoal">
                    Ver Catálogo
                  </Button>
                </Link>
                <a
                  href="https://wa.me/5511999999999?text=Olá,%20gostaria%20de%20fazer%20um%20pedido!"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  <Button variant="outline" className="border-2 border-charcoal text-charcoal hover:bg-beige font-mono">
                    <MessageCircle className="w-4 h-4 mr-2" />
                    WhatsApp
                  </Button>
                </a>
              </div>

              {/* Decorative elements */}
              <div className="pt-8 space-y-2">
                <div className="text-charcoal font-mono text-sm">
                  ✓ Ingredientes de qualidade
                </div>
                <div className="text-charcoal font-mono text-sm">
                  ✓ Feito artesanalmente
                </div>
                <div className="text-charcoal font-mono text-sm">
                  ✓ Entrega rápida
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Categories Preview */}
      <section className="py-16 bg-beige border-t-2 border-b-2 border-charcoal">
        <div className="container mx-auto px-4">
          <h3 className="font-script text-4xl text-charcoal mb-12 text-center">
            Nossas Categorias
          </h3>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {/* Doces */}
            <div className="bg-cream border-2 border-charcoal p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-6xl mb-4">🍫</div>
              <h4 className="font-script text-2xl text-charcoal mb-2">Doces</h4>
              <p className="font-mono text-charcoal text-sm mb-4">
                Bombons, Ovos de Páscoa, Tortas Doces e Bolos Especiais
              </p>
              <Link href="/catalogo?categoria=doces">
                <Button variant="outline" className="w-full border-2 border-charcoal text-charcoal font-mono">
                  Explorar
                </Button>
              </Link>
            </div>

            {/* Salgados */}
            <div className="bg-cream border-2 border-charcoal p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-6xl mb-4">🥐</div>
              <h4 className="font-script text-2xl text-charcoal mb-2">Salgados</h4>
              <p className="font-mono text-charcoal text-sm mb-4">
                Empadas, Quiches e Pastéis Salgados
              </p>
              <Link href="/catalogo?categoria=salgados">
                <Button variant="outline" className="w-full border-2 border-charcoal text-charcoal font-mono">
                  Explorar
                </Button>
              </Link>
            </div>

            {/* Especiais */}
            <div className="bg-cream border-2 border-charcoal p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-6xl mb-4">⭐</div>
              <h4 className="font-script text-2xl text-charcoal mb-2">Especiais</h4>
              <p className="font-mono text-charcoal text-sm mb-4">
                Encomendas Personalizadas e Combos Especiais
              </p>
              <Link href="/contato">
                <Button variant="outline" className="w-full border-2 border-charcoal text-charcoal font-mono">
                  Solicitar
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="text-center">
              <div className="text-4xl mb-4">🎨</div>
              <h4 className="font-script text-2xl text-charcoal mb-2">Artesanal</h4>
              <p className="font-mono text-charcoal text-sm">
                Cada produto é feito com dedicação e atenção aos detalhes
              </p>
            </div>

            <div className="text-center">
              <div className="text-4xl mb-4">🚚</div>
              <h4 className="font-script text-2xl text-charcoal mb-2">Entrega Rápida</h4>
              <p className="font-mono text-charcoal text-sm">
                Entregamos seus pedidos no prazo combinado
              </p>
            </div>

            <div className="text-center">
              <div className="text-4xl mb-4">💝</div>
              <h4 className="font-script text-2xl text-charcoal mb-2">Qualidade</h4>
              <p className="font-mono text-charcoal text-sm">
                Ingredientes selecionados para o melhor sabor
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-charcoal text-cream border-t-2 border-charcoal py-12">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div>
              <h5 className="font-script text-xl mb-4">Doces & Salgados</h5>
              <p className="font-mono text-sm">
                Confeitaria artesanal com produtos de qualidade
              </p>
            </div>

            <div>
              <h5 className="font-script text-xl mb-4">Contato</h5>
              <p className="font-mono text-sm">
                Email: contato@docesesalgados.com.br
              </p>
              <p className="font-mono text-sm">
                WhatsApp: (11) 99999-9999
              </p>
            </div>

            <div>
              <h5 className="font-script text-xl mb-4">Redes Sociais</h5>
              <div className="flex gap-4">
                <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
                  <Instagram className="w-6 h-6 hover:text-warm-orange transition" />
                </a>
                <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
                  <Facebook className="w-6 h-6 hover:text-warm-orange transition" />
                </a>
                <a href="https://wa.me" target="_blank" rel="noopener noreferrer">
                  <MessageCircle className="w-6 h-6 hover:text-warm-orange transition" />
                </a>
              </div>
            </div>
          </div>

          <div className="border-t border-cream pt-8 text-center font-mono text-sm">
            <p>&copy; 2026 Doces & Salgados. Todos os direitos reservados.</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
