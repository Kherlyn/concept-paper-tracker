/**
 * Image Optimization Utilities for Landing Page
 * Provides helpers for responsive images, lazy loading, and performance
 */

/**
 * Generate srcset for responsive images
 * @param {string} basePath - Base path to the image
 * @param {string} filename - Image filename without extension
 * @param {string} extension - Image extension (jpg, png, webp)
 * @param {array} sizes - Array of sizes [320, 640, 1024, 1920]
 * @returns {string} srcset string
 */
export function generateSrcSet(
    basePath,
    filename,
    extension,
    sizes = [320, 640, 1024, 1920]
) {
    return sizes
        .map((size) => `${basePath}/${filename}-${size}w.${extension} ${size}w`)
        .join(", ");
}

/**
 * Generate sizes attribute for responsive images
 * @param {object} breakpoints - Object with breakpoint definitions
 * @returns {string} sizes string
 */
export function generateSizes(
    breakpoints = {
        sm: "640px",
        md: "768px",
        lg: "1024px",
        xl: "1280px",
    }
) {
    return `
        (max-width: ${breakpoints.sm}) 100vw,
        (max-width: ${breakpoints.md}) 80vw,
        (max-width: ${breakpoints.lg}) 60vw,
        50vw
    `.trim();
}

/**
 * Lazy load images using Intersection Observer
 * @param {string} selector - CSS selector for images to lazy load
 */
export function lazyLoadImages(selector = "img[data-src]") {
    if ("IntersectionObserver" in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    if (img.dataset.srcset) {
                        img.srcset = img.dataset.srcset;
                    }
                    img.classList.remove("lazy");
                    imageObserver.unobserve(img);
                }
            });
        });

        const images = document.querySelectorAll(selector);
        images.forEach((img) => imageObserver.observe(img));
    } else {
        // Fallback for browsers without Intersection Observer
        const images = document.querySelectorAll(selector);
        images.forEach((img) => {
            img.src = img.dataset.src;
            if (img.dataset.srcset) {
                img.srcset = img.dataset.srcset;
            }
        });
    }
}

/**
 * Preload critical images
 * @param {array} images - Array of image URLs to preload
 */
export function preloadImages(images = []) {
    images.forEach((src) => {
        const link = document.createElement("link");
        link.rel = "preload";
        link.as = "image";
        link.href = src;
        document.head.appendChild(link);
    });
}

/**
 * Get optimized image format based on browser support
 * @returns {string} Preferred image format (webp, jpg, png)
 */
export function getOptimalImageFormat() {
    // Check for WebP support
    const canvas = document.createElement("canvas");
    if (canvas.getContext && canvas.getContext("2d")) {
        return canvas.toDataURL("image/webp").indexOf("data:image/webp") === 0
            ? "webp"
            : "jpg";
    }
    return "jpg";
}

/**
 * Create a placeholder for images while loading
 * @param {number} width - Image width
 * @param {number} height - Image height
 * @param {string} color - Placeholder color
 * @returns {string} Data URL for placeholder
 */
export function createPlaceholder(
    width = 1920,
    height = 1080,
    color = "#e5e7eb"
) {
    const canvas = document.createElement("canvas");
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext("2d");
    ctx.fillStyle = color;
    ctx.fillRect(0, 0, width, height);
    return canvas.toDataURL();
}

/**
 * Calculate aspect ratio padding for responsive images
 * @param {number} width - Image width
 * @param {number} height - Image height
 * @returns {string} Padding percentage
 */
export function getAspectRatioPadding(width, height) {
    return `${(height / width) * 100}%`;
}

/**
 * Optimize image loading performance
 * @param {HTMLImageElement} img - Image element
 * @param {object} options - Optimization options
 */
export function optimizeImage(img, options = {}) {
    const {
        loading = "lazy",
        decoding = "async",
        fetchpriority = "auto",
    } = options;

    img.loading = loading;
    img.decoding = decoding;
    if (fetchpriority !== "auto") {
        img.fetchpriority = fetchpriority;
    }
}

/**
 * Generate blur-up placeholder using canvas
 * @param {string} src - Image source
 * @param {number} blurAmount - Blur amount in pixels
 * @returns {Promise<string>} Blurred image data URL
 */
export async function generateBlurPlaceholder(src, blurAmount = 20) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = "Anonymous";
        img.onload = () => {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            // Use small dimensions for placeholder
            canvas.width = 40;
            canvas.height = 40;

            ctx.filter = `blur(${blurAmount}px)`;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            resolve(canvas.toDataURL());
        };
        img.onerror = reject;
        img.src = src;
    });
}

/**
 * Check if image is in viewport
 * @param {HTMLElement} element - Element to check
 * @param {number} threshold - Threshold in pixels
 * @returns {boolean} True if in viewport
 */
export function isInViewport(element, threshold = 0) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= -threshold &&
        rect.left >= -threshold &&
        rect.bottom <=
            (window.innerHeight || document.documentElement.clientHeight) +
                threshold &&
        rect.right <=
            (window.innerWidth || document.documentElement.clientWidth) +
                threshold
    );
}

/**
 * Progressive image loading component helper
 * @param {string} lowResSrc - Low resolution image source
 * @param {string} highResSrc - High resolution image source
 * @param {HTMLImageElement} imgElement - Image element
 */
export function progressiveImageLoad(lowResSrc, highResSrc, imgElement) {
    // Load low-res first
    imgElement.src = lowResSrc;
    imgElement.classList.add("blur-sm", "transition-all", "duration-300");

    // Load high-res in background
    const highResImg = new Image();
    highResImg.onload = () => {
        imgElement.src = highResSrc;
        imgElement.classList.remove("blur-sm");
    };
    highResImg.src = highResSrc;
}

export default {
    generateSrcSet,
    generateSizes,
    lazyLoadImages,
    preloadImages,
    getOptimalImageFormat,
    createPlaceholder,
    getAspectRatioPadding,
    optimizeImage,
    generateBlurPlaceholder,
    isInViewport,
    progressiveImageLoad,
};
