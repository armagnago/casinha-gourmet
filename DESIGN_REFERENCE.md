# Referências de Design - Loja Virtual de Doces e Salgados

## Estética Visual

A loja deve ter uma estética autêntica de **esboço desenhado à mão**, criando uma atmosfera criativa e artesanal que evoca confiança e qualidade artesanal.

### Elementos Visuais Principais

1. **Fundo**: Papel creme quente (tom natural, acolhedor)
2. **Linhas**: Orgânicas em carvão escuro, criando contornos irregulares
3. **Formas**: Geométricas imperfeitas, contornos tracejados
4. **Marcas**: Rascunhos visíveis, linhas de lápis, imperfeições propositais
5. **Atmosfera**: Blueprint pessoal e artístico, trabalho em progresso

### Tipografia

- **Cabeçalhos**: Script marcador ousado (estilo desenhado à mão, dinâmico)
- **Corpo**: Monoespaçada de máquina de escrever (criando contraste e autenticidade)
- **Destaque**: Combinação de estilos para criar hierarquia visual

## Paleta de Cores

Cores quentes e acolhedoras típicas de confeitaria:

| Cor | Hex | RGB | Uso |
|-----|-----|-----|-----|
| Creme Quente | #F5E6D3 | (245, 230, 211) | Fundo principal |
| Carvão Escuro | #2C2C2C | (44, 44, 44) | Linhas, textos principais |
| Marrom Chocolate | #8B6F47 | (139, 111, 71) | Acentos, destaques |
| Laranja Quente | #E8956D | (232, 149, 109) | Botões, CTAs |
| Rosa Suave | #D4A5A5 | (212, 165, 165) | Acentos secundários |
| Bege Neutro | #E8DCC8 | (232, 220, 200) | Backgrounds secundários |
| Verde Menta | #A8C5A0 | (168, 197, 160) | Elementos decorativos |

## Componentes de Design

### Padrões e Texturas

- Papel enrugado/texturizado como fundo
- Linhas pontilhadas para separadores
- Marcas de rascunho em elementos decorativos
- Sombras suaves para profundidade

### Ícones e Elementos

- Ícones desenhados à mão com linhas irregulares
- Elementos decorativos (flores, folhas, linhas doodle)
- Setas e indicadores em estilo sketch
- Caixas e frames com bordas desenhadas

### Componentes UI

- Botões com bordas desenhadas e textura
- Cards com sombra suave e bordas irregulares
- Inputs com estilo de papel manuscrito
- Badges e labels com design artístico

## Referências Visuais Coletadas

1. **Ista Bake Studio** - Design minimalista moderno com tipografia ousada
2. **BakesbyTiss** - Estética elegante com esquema de cores neutro
3. **Lovejoy Bakers** - Layout em grid com acentos florais ilustrados
4. **Clyde's Donuts** - Design vibrante com GIFs animados de produtos
5. **A Spoon Fulla Sugar** - Elementos desenhados à mão, estilo aquarela

## Implementação no Código

### CSS Variables (Tailwind)

```css
@theme {
  colors {
    cream: #F5E6D3;
    charcoal: #2C2C2C;
    chocolate: #8B6F47;
    warm-orange: #E8956D;
    soft-pink: #D4A5A5;
    beige: #E8DCC8;
    mint: #A8C5A0;
  }
  
  fontFamily {
    script: 'Caveat, cursive';
    mono: 'Courier Prime, monospace';
  }
}
```

### Fontes Google

- **Script**: Caveat, Satisfy, Pacifico
- **Monoespaçada**: Courier Prime, IBM Plex Mono
- **Corpo**: Inter, Poppins (com peso variado)

## Responsividade

- Design mobile-first com breakpoints em 640px, 768px, 1024px
- Elementos adaptáveis mantendo a estética sketch
- Imagens responsivas com aspect ratio consistente

## Acessibilidade

- Contraste suficiente entre texto e fundo
- Texto legível em todos os tamanhos
- Elementos interativos com foco visível
- Navegação por teclado completa

