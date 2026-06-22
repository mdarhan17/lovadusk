document.addEventListener("DOMContentLoaded", function () {
    console.log("LOVA DUSK loaded");
});

document.addEventListener("DOMContentLoaded", function () {
    const revealTargets = document.querySelectorAll(
        ".featured-section, .product-card, .drop-card, .auth-card, .cart-item, .checkout-form, .cart-summary, .review-card, .policy-box, .brand-story"
    );

    revealTargets.forEach(function (item) {
        item.classList.add("reveal");
    });

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    }, {
        threshold: 0.12
    });

    revealTargets.forEach(function (item) {
        observer.observe(item);
    });
});
