import React from 'react';
import './button.css';

interface ButtonProps {
    variant?: "primary" | "secondary";
    size?: "small" | "medium" | "large";
    onClick?: () => void;
    children: React.ReactNode;
}

const styles = {
    base: "padding border-none cursor-pointer",
    variants: {
        primary: "bg-blue",
        secondary: "bg-gray"
    },
    sizes: {
        small: "scale-0.8",
        medium: "scale-1.0",
        large: "scale-1.2"
    }
};

export const Button = ({
    variant = "primary",
    size = "medium",
    onClick,
    children
}: ButtonProps) => {
    const selectedVariant = styles.variants[variant];
    const selectedSize = styles.sizes[size];

    return (
        <button
            onClick={onClick}
            className={`${styles.base} ${selectedVariant} ${selectedSize}`}
        >
            {children}
        </button>
    );
};