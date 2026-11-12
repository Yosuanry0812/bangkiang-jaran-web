const colors = require('tailwindcss/colors');

const config = {
    "darkMode": "class",
    "content": [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./public/js/*.js"
    ],
    "theme": {
        "extend": {
            "colors": {
                "on-primary-fixed-variant": "#005143",
                "outline": "#6e7975",
                "surface-container": "#f0eee9",
                "tertiary": "#5b442a",
                "on-background": "#1b1c19",
                "on-surface-variant": "#3e4945",
                "primary": "#005344",
                "surface-container-highest": "#e4e2dd",
                "surface": "#fbf9f4",
                "on-secondary-fixed-variant": "#23501e",
                "on-primary-fixed": "#00201a",
                "surface-container-low": "#f5f3ee",
                "tertiary-fixed-dim": "#e3c19f",
                "primary-fixed": "#9df3dc",
                "secondary": "#3b6934",
                "primary-fixed-dim": "#81d6c0",
                "on-tertiary": "#ffffff",
                "secondary-fixed": "#bcf0ae",
                "inverse-surface": "#30312e",
                "on-secondary-fixed": "#002201",
                "surface-dim": "#dbdad5",
                "on-tertiary-fixed-variant": "#5a4229",
                "outline-variant": "#bec9c4",
                "secondary-container": "#b9eeab",
                "on-tertiary-fixed": "#291803",
                "error": "#ba1a1a",
                "surface-variant": "#e4e2dd",
                "on-primary": "#ffffff",
                "inverse-on-surface": "#f2f1ec",
                "background": "#fbf9f4",
                "inverse-primary": "#81d6c0",
                "surface-container-lowest": "#ffffff",
                "on-error-container": "#93000a",
                "tertiary-container": "#755b40",
                "surface-container-high": "#eae8e3",
                "on-primary-container": "#96ebd5",
                "tertiary-fixed": "#ffddbb",
                "on-secondary-container": "#3f6d38",
                "surface-tint": "#006b59",
                "on-tertiary-container": "#f8d5b2",
                "on-error": "#ffffff",
                "on-secondary": "#ffffff",
                "on-surface": "#1b1c19",
                "primary-container": "#006d5b",
                "surface-bright": "#fbf9f4",
                "secondary-fixed-dim": "#a1d494",
                "error-container": "#ffdad6",
                "forest": "#1B3A2D",
                "leaf": "#2D5940",
                "moss": "#4A7C59",
                "sage": "#7FAF8A",
                "mist": "#C8DDD0",
                "ivory": "#F7F3EE",
                "warm": "#EDE8E1",
                "gold": "#C9A96E",
                "sand": "#D4C5A9",
                "charcoal": "#1C1C1C",
                "ink": "#2C2C2C",
                "pebble": "#A0A0A0",
                "cream": "#F7F3EE",
                "deep": "#1B3A2D"
            },
            "spacing": {
                "xl": "80px",
                "container-max": "1280px",
                "lg": "48px",
                "md": "24px",
                "sm": "12px",
                "base": "8px",
                "xs": "4px",
                "gutter": "24px",
                "section": "96px"
            },
            "fontFamily": {
                "serif": [
                    "Playfair Display",
                    "Georgia",
                    "serif"
                ],
                "sans": [
                    "DM Sans",
                    "system-ui",
                    "sans-serif"
                ],
                "display": [
                    "Playfair Display",
                    "serif"
                ],
                "body": [
                    "DM Sans",
                    "sans-serif"
                ],
                "editorial": [
                    "Cormorant Garamond",
                    "Georgia",
                    "serif"
                ]
            },
            "fontSize": {
                "display-mobile": [
                    "40px",
                    {
                        "lineHeight": "1.2",
                        "fontWeight": "700"
                    }
                ],
                "headline-md": [
                    "32px",
                    {
                        "lineHeight": "1.3",
                        "fontWeight": "600"
                    }
                ],
                "headline-sm": [
                    "24px",
                    {
                        "lineHeight": "1.4",
                        "fontWeight": "600"
                    }
                ],
                "body-lg": [
                    "18px",
                    {
                        "lineHeight": "1.6",
                        "fontWeight": "400"
                    }
                ],
                "body-md": [
                    "16px",
                    {
                        "lineHeight": "1.6",
                        "fontWeight": "400"
                    }
                ],
                "label-md": [
                    "14px",
                    {
                        "lineHeight": "1.2",
                        "letterSpacing": "0.05em",
                        "fontWeight": "600"
                    }
                ],
                "caption": [
                    "12px",
                    {
                        "lineHeight": "1.4",
                        "fontWeight": "500"
                    }
                ]
            }
        }
    }
};

config.theme.extend.colors.stone = { ...colors.stone, DEFAULT: "#6B6B6B" };
config.plugins = [require('@tailwindcss/forms')];

module.exports = config;
