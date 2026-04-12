import React from 'react';
import './button.css';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    variant?: "primary" | "secondary";
    size?: "small" | "medium" | "large";
}

const styles = {
    base: "padding border-none cursor-pointer",
    variants: {
        primary: "bg-blue",
        secondary: "bg-gray"
    },
    sizes: {
        small: "scale-sm",
        medium: "scale-md",
        large: "scale-lg"
    }
};

export const Button = ({
                           variant = "primary",
                           size = "medium",
                           children,
                           ...props
                       }: ButtonProps) => {
    const selectedVariant = styles.variants[variant];
    const selectedSize = styles.sizes[size];

    return (
        <button
            {...props}
            className={`${styles.base} ${selectedVariant} ${selectedSize}`}
        >
            {children}
        </button>
    );
};