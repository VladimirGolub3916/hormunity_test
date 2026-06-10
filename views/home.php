<?php

/** @var App\Models\Product[] $products */
/** @var array $cart */
/** @var array $favorites */

$favoriteIds = $favorites['ids'] ?? [];
$initialState = [
    'cart' => $cart,
    'favorites' => $favorites,
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hormunity</title>
  <meta name="description" content="Hormunity landing page layout based on the provided Figma export.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500&family=Inter:wght@380;460;520;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $this->e($this->url('/styles.css')) ?>">
</head>
<body>
  <div class="site-shell">
    <section class="hero" id="top">
      <header class="site-header" data-header>
        <a class="brand" href="#top" aria-label="Hormunity home">
          <span class="brand-mark" aria-hidden="true">
            <span></span><span></span><span></span><span></span><span></span>
          </span>
          <span class="brand-word">HORMUNITY</span>
        </a>

        <nav class="main-nav" data-nav aria-label="Main navigation">
          <a href="#shop">Shop <i data-lucide="chevron-down"></i></a>
          <a href="#tests">Blood tests</a>
          <a href="#services">Services <i data-lucide="chevron-down"></i></a>
          <a href="#learn">Learn <i data-lucide="chevron-down"></i></a>
          <a href="#about">About <i data-lucide="chevron-down"></i></a>
        </nav>

        <div class="header-actions">
          <button class="icon-button search-toggle" type="button" aria-label="Search">
            <i data-lucide="search"></i>
          </button>
          <button class="icon-button account-toggle" type="button" aria-label="Account">
            <i data-lucide="user-round"></i>
          </button>
          <button class="icon-button cart-toggle" type="button" aria-label="Open cart" data-cart-open>
            <i data-lucide="shopping-cart"></i>
            <span class="cart-count" data-cart-count><?= (int) ($cart['count'] ?? 0) ?></span>
          </button>
          <a class="start-button" href="#tests">Start testing</a>
          <button class="icon-button menu-toggle" type="button" aria-label="Open menu" data-menu-toggle>
            <i data-lucide="menu"></i>
          </button>
        </div>
      </header>

      <div class="hero-inner">
        <div class="hero-copy">
          <div class="service-pills" aria-label="Service categories">
            <span><i data-lucide="dna"></i> Personal care</span>
            <span><i data-lucide="layers-3"></i> Protocols</span>
            <span><i data-lucide="microscope"></i> Diagnostics</span>
          </div>

          <h1>A deeper look into your health &mdash; <em>testing &amp; clinical insight</em></h1>
          <p>
            Comprehensive diagnostics designed to reveal what standard evaluations miss,
            interpreted by medical experts for meaningful, actionable clarity.
          </p>

          <div class="hero-buttons">
            <a class="primary-button" href="#tests">
              Start testing
              <i data-lucide="arrow-right"></i>
            </a>
            <a class="ghost-button" href="#learn">How it works</a>
          </div>
        </div>

        <a class="promo-card" href="#shop">
          <span class="promo-label"><span></span> New</span>
          <strong>Free UPS Ground Shipping on domestic orders over $150</strong>
          <i data-lucide="arrow-up-right"></i>
        </a>
      </div>
    </section>

    <main>
      <section class="products-section" id="shop" aria-labelledby="products-title">
        <div class="section-heading">
          <div>
            <p class="eyebrow"><span></span> Best seller</p>
            <h2 id="products-title">Highlighted <em>Products</em></h2>
          </div>
          <a class="shop-link" href="#shop">Visit shop</a>
        </div>

        <div class="product-carousel">
          <button class="slider-button slider-prev" type="button" aria-label="Previous products" data-slide="prev">
            <i data-lucide="chevron-left"></i>
          </button>

          <div class="product-track" data-product-track>
            <?php foreach ($products as $product): ?>
              <?php
              $classes = ['product-card'];

              if ($product->featured()) {
                  $classes[] = 'is-featured';
              }

              if ($product->muted()) {
                  $classes[] = 'is-muted';
              }

              $favoriteActive = in_array($product->id(), $favoriteIds, true);
              ?>
              <article class="<?= $this->e(implode(' ', $classes)) ?>" data-product-card>
                <button
                  class="favorite-button<?= $favoriteActive ? ' is-active' : '' ?>"
                  type="button"
                  aria-label="Toggle <?= $this->e($product->name()) ?> favorite"
                  data-favorite-toggle
                  data-product-id="<?= $this->e($product->id()) ?>"
                >
                  <i data-lucide="heart"></i>
                </button>

                <?php if ($product->tagLabel() !== null): ?>
                  <span class="product-tag <?= $this->e($product->tagClass() ?? '') ?>"><?= $this->e($product->tagLabel()) ?></span>
                <?php endif; ?>

                <div class="product-image">
                  <img src="<?= $this->e($this->url($product->image())) ?>" alt="<?= $this->e($product->alt()) ?>">
                </div>
                <h3><?= $this->e($product->name()) ?></h3>
                <p><?= $this->e($product->description()) ?></p>
                <div class="product-bottom">
                  <span class="price">
                    <?= $this->e($this->money($product->price())) ?>
                    <?php if ($product->oldPrice() !== null): ?>
                      <del><?= $this->e($this->money($product->oldPrice())) ?></del>
                    <?php endif; ?>
                  </span>
                  <button
                    class="add-button"
                    type="button"
                    data-add-cart
                    data-product-id="<?= $this->e($product->id()) ?>"
                  >Add</button>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

          <button class="slider-button slider-next" type="button" aria-label="Next products" data-slide="next">
            <i data-lucide="chevron-right"></i>
          </button>
        </div>
      </section>
    </main>

    <footer class="site-footer" id="about">
      <div class="footer-inner">
        <div class="footer-brand">
          <a class="brand brand-footer" href="#top" aria-label="Hormunity home">
            <span class="brand-mark" aria-hidden="true">
              <span></span><span></span><span></span><span></span><span></span>
            </span>
            <span class="brand-word">HORMUNITY</span>
          </a>
          <p>With a robust foundation in functional medicine, we specialize in addressing the root causes of health.</p>
          <div class="social-list" aria-label="Social links">
            <a href="#" aria-label="Instagram"><i data-lucide="camera"></i></a>
            <a href="#" aria-label="TikTok"><i data-lucide="music-2"></i></a>
            <a href="#" aria-label="YouTube"><i data-lucide="play"></i></a>
            <a href="#" aria-label="Facebook"><i data-lucide="message-circle"></i></a>
          </div>
        </div>

        <div class="footer-links">
          <div>
            <h3>Quick links</h3>
            <a href="#shop">Shop</a>
            <a href="#tests">Blood tests</a>
            <a href="#services">Lab analysis</a>
            <a href="#services">Consultations</a>
          </div>
          <div>
            <h3>Learn</h3>
            <a href="#learn">Articles</a>
            <a href="#learn">Protocols</a>
          </div>
          <div>
            <h3>About</h3>
            <a href="#about">About us</a>
            <a href="#about">Contact</a>
            <a href="#about">FAQ</a>
          </div>
          <div class="contact-column">
            <h3>Contact us</h3>
            <a href="mailto:info@hormunity.com"><i data-lucide="mail"></i> info@hormunity.com</a>
            <a href="tel:+17752556771"><i data-lucide="phone"></i> +1 (775) 255-6771</a>
            <address><i data-lucide="map-pin"></i> 3495 Lakeside Drive<br>Ph 1261<br>Reno, NV 89509, USA</address>
          </div>
        </div>

        <div class="footer-bottom">
          <p>&copy; 2022 - 2026 Hormunity LLC. All rights reserved</p>
          <div>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms &amp; Conditions</a>
          </div>
        </div>
      </div>
    </footer>
  </div>

  <div class="cart-overlay" data-cart-overlay hidden></div>
  <aside class="cart-drawer" data-cart-drawer aria-hidden="true" aria-labelledby="cart-title">
    <div class="cart-header">
      <h2 id="cart-title">Cart</h2>
      <button class="icon-button cart-close" type="button" aria-label="Close cart" data-cart-close>
        <i data-lucide="x"></i>
      </button>
    </div>
    <div class="cart-body" data-cart-body>
      <div class="cart-empty" data-cart-empty>
        <i data-lucide="shopping-cart"></i>
        <h3>Your cart is empty</h3>
        <p>Fill your cart with amazing items.</p>
        <a class="start-button dark" href="#shop" data-cart-close-link>Visit shop</a>
      </div>
      <div class="cart-items" data-cart-items></div>
    </div>
    <div class="cart-message" data-cart-message hidden></div>
    <div class="cart-footer" data-cart-footer hidden>
      <div class="cart-total">
        <span>Total</span>
        <strong data-cart-total>$0.00</strong>
      </div>
      <button class="checkout-button" type="button" data-checkout>Checkout</button>
    </div>
  </aside>

  <script>
    window.AppBasePath = <?= json_encode($this->basePath(), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.AppState = <?= json_encode($initialState, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  </script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <script src="<?= $this->e($this->url('/script.js')) ?>"></script>
</body>
</html>
