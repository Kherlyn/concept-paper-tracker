import { useEffect, useRef, useState } from "react";

/**
 * Custom hook for scroll-based animations
 * Triggers animations when elements enter the viewport
 */
export function useScrollAnimation(options = {}) {
    const {
        threshold = 0.1,
        rootMargin = "0px",
        triggerOnce = true,
        animationClass = "animate-fade-in-up",
    } = options;

    const elementRef = useRef(null);
    const [isVisible, setIsVisible] = useState(false);

    useEffect(() => {
        const element = elementRef.current;
        if (!element) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setIsVisible(true);
                    element.classList.add(animationClass);

                    if (triggerOnce) {
                        observer.unobserve(element);
                    }
                } else if (!triggerOnce) {
                    setIsVisible(false);
                    element.classList.remove(animationClass);
                }
            },
            {
                threshold,
                rootMargin,
            }
        );

        observer.observe(element);

        return () => {
            if (element) {
                observer.unobserve(element);
            }
        };
    }, [threshold, rootMargin, triggerOnce, animationClass]);

    return { elementRef, isVisible };
}

/**
 * Hook for parallax scroll effect
 */
export function useParallax(speed = 0.5) {
    const elementRef = useRef(null);
    const [offset, setOffset] = useState(0);

    useEffect(() => {
        const handleScroll = () => {
            if (elementRef.current) {
                const rect = elementRef.current.getBoundingClientRect();
                const scrolled = window.pageYOffset;
                const rate = scrolled * speed;
                setOffset(rate);
            }
        };

        window.addEventListener("scroll", handleScroll, { passive: true });
        return () => window.removeEventListener("scroll", handleScroll);
    }, [speed]);

    return { elementRef, offset };
}

/**
 * Hook for scroll progress indicator
 */
export function useScrollProgress() {
    const [progress, setProgress] = useState(0);

    useEffect(() => {
        const handleScroll = () => {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop =
                window.pageYOffset || document.documentElement.scrollTop;
            const scrollPercent =
                (scrollTop / (documentHeight - windowHeight)) * 100;
            setProgress(Math.min(100, Math.max(0, scrollPercent)));
        };

        window.addEventListener("scroll", handleScroll, { passive: true });
        handleScroll(); // Initial calculation

        return () => window.removeEventListener("scroll", handleScroll);
    }, []);

    return progress;
}

/**
 * Hook for detecting scroll direction
 */
export function useScrollDirection() {
    const [scrollDirection, setScrollDirection] = useState("up");
    const [lastScrollY, setLastScrollY] = useState(0);

    useEffect(() => {
        const handleScroll = () => {
            const currentScrollY = window.pageYOffset;

            if (currentScrollY > lastScrollY) {
                setScrollDirection("down");
            } else if (currentScrollY < lastScrollY) {
                setScrollDirection("up");
            }

            setLastScrollY(currentScrollY);
        };

        window.addEventListener("scroll", handleScroll, { passive: true });
        return () => window.removeEventListener("scroll", handleScroll);
    }, [lastScrollY]);

    return scrollDirection;
}

/**
 * Hook for smooth scroll to element
 */
export function useSmoothScroll() {
    const scrollToElement = (elementId, offset = 0) => {
        const element = document.getElementById(elementId);
        if (element) {
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition =
                elementPosition + window.pageYOffset - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth",
            });
        }
    };

    const scrollToTop = () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    };

    return { scrollToElement, scrollToTop };
}

/**
 * Hook for sticky header behavior
 */
export function useStickyHeader(threshold = 100) {
    const [isSticky, setIsSticky] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            const scrollY = window.pageYOffset;
            setIsSticky(scrollY > threshold);
        };

        window.addEventListener("scroll", handleScroll, { passive: true });
        handleScroll(); // Initial check

        return () => window.removeEventListener("scroll", handleScroll);
    }, [threshold]);

    return isSticky;
}

/**
 * Hook for viewport detection
 */
export function useInViewport(options = {}) {
    const { threshold = 0, rootMargin = "0px" } = options;
    const elementRef = useRef(null);
    const [isInViewport, setIsInViewport] = useState(false);

    useEffect(() => {
        const element = elementRef.current;
        if (!element) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                setIsInViewport(entry.isIntersecting);
            },
            { threshold, rootMargin }
        );

        observer.observe(element);

        return () => {
            if (element) {
                observer.unobserve(element);
            }
        };
    }, [threshold, rootMargin]);

    return { elementRef, isInViewport };
}

/**
 * Hook for staggered animations
 */
export function useStaggeredAnimation(itemCount, delay = 100) {
    const [visibleItems, setVisibleItems] = useState([]);
    const containerRef = useRef(null);

    useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    // Stagger the animation of items
                    for (let i = 0; i < itemCount; i++) {
                        setTimeout(() => {
                            setVisibleItems((prev) => [...prev, i]);
                        }, i * delay);
                    }
                    observer.unobserve(entry.target);
                }
            },
            { threshold: 0.1 }
        );

        if (containerRef.current) {
            observer.observe(containerRef.current);
        }

        return () => {
            if (containerRef.current) {
                observer.unobserve(containerRef.current);
            }
        };
    }, [itemCount, delay]);

    return { containerRef, visibleItems };
}

/**
 * Hook for scroll-triggered counter animation
 */
export function useCountUp(end, duration = 2000, options = {}) {
    const { threshold = 0.5, decimals = 0 } = options;
    const [count, setCount] = useState(0);
    const elementRef = useRef(null);
    const hasAnimated = useRef(false);

    useEffect(() => {
        const element = elementRef.current;
        if (!element) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting && !hasAnimated.current) {
                    hasAnimated.current = true;

                    const startTime = Date.now();
                    const startValue = 0;

                    const animate = () => {
                        const currentTime = Date.now();
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);

                        // Easing function (ease-out)
                        const easeOut = 1 - Math.pow(1 - progress, 3);
                        const currentCount =
                            startValue + (end - startValue) * easeOut;

                        setCount(Number(currentCount.toFixed(decimals)));

                        if (progress < 1) {
                            requestAnimationFrame(animate);
                        }
                    };

                    requestAnimationFrame(animate);
                    observer.unobserve(element);
                }
            },
            { threshold }
        );

        observer.observe(element);

        return () => {
            if (element) {
                observer.unobserve(element);
            }
        };
    }, [end, duration, threshold, decimals]);

    return { elementRef, count };
}

export default {
    useScrollAnimation,
    useParallax,
    useScrollProgress,
    useScrollDirection,
    useSmoothScroll,
    useStickyHeader,
    useInViewport,
    useStaggeredAnimation,
    useCountUp,
};
