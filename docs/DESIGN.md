---
name: Tropical Sanctuary
colors:
  surface: '#fbf9f4'
  surface-dim: '#dbdad5'
  surface-bright: '#fbf9f4'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f3ee'
  surface-container: '#f0eee9'
  surface-container-high: '#eae8e3'
  surface-container-highest: '#e4e2dd'
  on-surface: '#1b1c19'
  on-surface-variant: '#3e4945'
  inverse-surface: '#30312e'
  inverse-on-surface: '#f2f1ec'
  outline: '#6e7975'
  outline-variant: '#bec9c4'
  surface-tint: '#006b59'
  primary: '#005344'
  on-primary: '#ffffff'
  primary-container: '#006d5b'
  on-primary-container: '#96ebd5'
  inverse-primary: '#81d6c0'
  secondary: '#3b6934'
  on-secondary: '#ffffff'
  secondary-container: '#b9eeab'
  on-secondary-container: '#3f6d38'
  tertiary: '#5b442a'
  on-tertiary: '#ffffff'
  tertiary-container: '#755b40'
  on-tertiary-container: '#f8d5b2'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#9df3dc'
  primary-fixed-dim: '#81d6c0'
  on-primary-fixed: '#00201a'
  on-primary-fixed-variant: '#005143'
  secondary-fixed: '#bcf0ae'
  secondary-fixed-dim: '#a1d494'
  on-secondary-fixed: '#002201'
  on-secondary-fixed-variant: '#23501e'
  tertiary-fixed: '#ffddbb'
  tertiary-fixed-dim: '#e3c19f'
  on-tertiary-fixed: '#291803'
  on-tertiary-fixed-variant: '#5a4229'
  background: '#fbf9f4'
  on-background: '#1b1c19'
  surface-variant: '#e4e2dd'
typography:
  display-lg:
    fontFamily: Playfair Display
    fontSize: 64px
    fontWeight: '700'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 40px
    fontWeight: '700'
    lineHeight: '1.2'
  headline-md:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '600'
    lineHeight: '1.3'
  headline-sm:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.4'
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: '1.2'
    letterSpacing: 0.05em
  caption:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '500'
    lineHeight: '1.4'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 12px
  md: 24px
  lg: 48px
  xl: 80px
  container-max: 1280px
  gutter: 24px
---

## Brand & Style
The design system is crafted for a premium e-tourism experience, specifically tailored to the lush, majestic environment of Bangkiang Jaran Waterfall. The brand personality is "Tropical Elegance"—a blend of high-end hospitality and raw, adventurous nature. It aims to evoke a sense of serenity, discovery, and grounded luxury.

The visual style utilizes **Minimalism** with **Tactile** influences. It prioritizes generous negative space to allow high-quality photography of Bali’s landscapes to breathe. While the layout is clean and modern, the use of organic textures and soft, layered depth prevents it from feeling sterile, creating a digital environment that feels as welcoming as a high-end jungle eco-resort.

## Colors
The palette is rooted in the Balinese landscape. The **Primary Emerald (#006D5B)** represents the deep pools and dense foliage of the waterfall site. The **Secondary Leaf Green (#2D5A27)** provides natural depth for accents and hover states. **Light Wood Brown (#C4A484)** is used for functional highlights, evoking the artisanal craftsmanship and timber structures found in Gianyar.

The **Background (#F9F7F2)** is an off-white cream that reduces eye strain and provides a warmer, more organic feel than pure white. **Deep Charcoal (#2C2C2C)** ensures high legibility for all text elements while maintaining a softer contrast than pitch black.

## Typography
This design system employs a sophisticated typographic pairing to balance heritage and modern utility. **Playfair Display** is used for all headings to provide a timeless, editorial feel that suggests exclusivity and adventure. **Plus Jakarta Sans** is the primary typeface for body copy and interface elements, chosen for its friendly, open apertures and excellent legibility across all screen sizes.

For large display headers on desktop, use a slight negative letter-spacing to create a tighter, more "designed" look. Labels should consistently use uppercase with increased tracking to distinguish them from standard body text.

## Layout & Spacing
The layout philosophy centers on a **Fluid Grid** with generous vertical rhythm to mirror the sense of space found in nature.

- **Desktop:** A 12-column grid with 24px gutters. Use wide 80px (xl) margins between major sections to prevent visual clutter.
- **Tablet:** A 6-column grid with 24px gutters and 32px side margins. 
- **Mobile:** A 2-column grid with 16px gutters and 20px side margins.

Content should follow an 8px base unit for all internal component spacing (padding, gaps). Hero sections should utilize "safe areas" where text is offset from the center to allow the background waterfall imagery to remain the focal point.

## Elevation & Depth
This design system avoids harsh dropshadows. Instead, it uses **Tonal Layers** and **Ambient Shadows** to create a sense of soft lifting.

- **Level 0 (Base):** Off-white background (#F9F7F2).
- **Level 1 (Cards/Inputs):** Pure white surfaces (#FFFFFF) with a very soft, diffused shadow: `0px 4px 20px rgba(0, 50, 40, 0.04)`.
- **Level 2 (Overlays/Modals):** Pure white surfaces with a deeper ambient shadow: `0px 12px 40px rgba(0, 50, 40, 0.08)`.

Transitions between levels should feel organic; use subtle "glass" effects (low-opacity white with a 10px backdrop blur) for sticky navigation bars to maintain a connection with the content beneath.

## Shapes
The shape language is "Soft-Organic." Square corners are avoided to maintain a friendly and natural aesthetic. 

- **Standard Buttons/Inputs:** 12px border radius.
- **Cards & Large Containers:** 24px border radius.
- **Selection Chips:** Fully rounded (pill-shaped).

When images are used in a grid, they should maintain the 24px radius to ensure consistency with the container language.

## Components

### Buttons
- **Primary:** Filled Emerald (#006D5B) with white text. 12px radius. Subtle lift on hover.
- **Secondary:** Outlined Emerald with a 1.5px border.
- **Tertiary:** Wood Brown (#C4A484) text with a simple underline, used for less prominent actions.

### Cards
Cards are the primary vessel for tour packages. They feature a 24px radius, pure white background, and a full-bleed top image. Content padding should be a generous 24px.

### Input Fields
Inputs use a light grey stroke (5% Deep Charcoal) and 12px radius. On focus, the stroke changes to Primary Emerald with a soft 4px outer glow.

### Chips & Tags
Used for categories like "Adventure," "Family-Friendly," or "Guided." These are pill-shaped with a light Leaf Green tint (#2D5A27 at 10% opacity) and dark green text.

### Icons
Use **Minimalist Outline Icons** with a 1.5px stroke weight. Icons should be monochrome (Deep Charcoal) to avoid competing with the lush photography.

### Navigation
The desktop navigation is transparent on hero images, transitioning to a blurred "glass" white on scroll. Links use Plus Jakarta Sans Bold in 14px uppercase.