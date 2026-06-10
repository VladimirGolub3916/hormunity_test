const initIcons = () => {
  if (window.lucide) {
    window.lucide.createIcons();
  }
};

const body = document.body;
const menuToggle = document.querySelector("[data-menu-toggle]");
const nav = document.querySelector("[data-nav]");
const cartOpenButtons = document.querySelectorAll("[data-cart-open]");
const cartCloseButtons = document.querySelectorAll("[data-cart-close], [data-cart-close-link]");
const cartOverlay = document.querySelector("[data-cart-overlay]");
const cartDrawer = document.querySelector("[data-cart-drawer]");
const cartCount = document.querySelector("[data-cart-count]");
const cartItems = document.querySelector("[data-cart-items]");
const cartEmpty = document.querySelector("[data-cart-empty]");
const cartFooter = document.querySelector("[data-cart-footer]");
const cartTotal = document.querySelector("[data-cart-total]");
const cartMessage = document.querySelector("[data-cart-message]");
const checkoutButton = document.querySelector("[data-checkout]");
const productTrack = document.querySelector("[data-product-track]");
const basePath = (window.AppBasePath || "").replace(/\/$/, "");
const state = {
  cart: window.AppState?.cart ?? { items: [], count: 0, total: 0 },
  favorites: window.AppState?.favorites ?? { ids: [], count: 0 },
};

initIcons();

const closeMenu = () => {
  body.classList.remove("menu-open");
  menuToggle?.setAttribute("aria-label", "Open menu");
};

const openCart = () => {
  if (cartOverlay) {
    cartOverlay.hidden = false;
  }

  body.classList.add("cart-open");
  cartDrawer?.setAttribute("aria-hidden", "false");
  closeMenu();
};

const closeCart = () => {
  body.classList.remove("cart-open");
  cartDrawer?.setAttribute("aria-hidden", "true");

  window.setTimeout(() => {
    if (!body.classList.contains("cart-open") && cartOverlay) {
      cartOverlay.hidden = true;
    }
  }, 220);
};

const formatPrice = (value) => `$${Number(value).toFixed(2)}`;

const showCartMessage = (message, isError = false) => {
  if (!cartMessage) {
    return;
  }

  cartMessage.hidden = false;
  cartMessage.textContent = message;
  cartMessage.classList.toggle("is-error", isError);
};

const hideCartMessage = () => {
  if (!cartMessage) {
    return;
  }

  cartMessage.hidden = true;
  cartMessage.textContent = "";
  cartMessage.classList.remove("is-error");
};

const apiUrl = (url) => {
  if (/^[a-z][a-z0-9+.-]*:/i.test(url)) {
    return url;
  }

  return `${basePath}/${url.replace(/^\/+/, "")}`;
};

const apiPost = async (url, payload = {}) => {
  const response = await fetch(apiUrl(url), {
    method: "POST",
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify(payload),
  });
  const data = await response.json().catch(() => ({}));

  if (!response.ok) {
    throw new Error(data.error || "Request failed");
  }

  return data;
};

const createCartItem = (item) => {
  const row = document.createElement("article");
  const info = document.createElement("div");
  const title = document.createElement("h3");
  const meta = document.createElement("p");
  const remove = document.createElement("button");
  const removeText = document.createElement("span");

  row.className = "cart-item";
  title.textContent = item.name;
  meta.textContent = `${item.quantity} x ${formatPrice(item.price)}`;
  remove.type = "button";
  remove.dataset.removeCart = item.id;
  remove.setAttribute("aria-label", `Remove ${item.name}`);
  removeText.setAttribute("aria-hidden", "true");
  removeText.textContent = "x";

  remove.append(removeText);
  info.append(title, meta);
  row.append(info, remove);

  return row;
};

const renderCart = (cartState) => {
  state.cart = cartState ?? { items: [], count: 0, total: 0 };

  if (cartCount) {
    cartCount.textContent = state.cart.count;
  }

  if (cartItems) {
    cartItems.innerHTML = "";
    state.cart.items.forEach((item) => cartItems.append(createCartItem(item)));
  }

  if (cartEmpty) {
    cartEmpty.hidden = state.cart.count > 0;
  }

  if (cartFooter) {
    cartFooter.hidden = state.cart.count === 0;
  }

  if (cartTotal) {
    cartTotal.textContent = formatPrice(state.cart.total);
  }

  if (checkoutButton) {
    checkoutButton.disabled = state.cart.count === 0;
  }
};

const syncFavoriteButtons = () => {
  const ids = new Set(state.favorites.ids ?? []);

  document.querySelectorAll("[data-favorite-toggle]").forEach((button) => {
    const active = ids.has(button.dataset.productId);
    button.classList.toggle("is-active", active);
    button.setAttribute("aria-pressed", active ? "true" : "false");
  });
};

const productCards = () => Array.from(productTrack?.querySelectorAll("[data-product-card]") ?? []);

const syncProductCarousel = () => {
  const cards = productCards();

  cards.forEach((card) => card.classList.remove("is-muted", "is-featured"));

  if (cards.length === 0) {
    return;
  }

  const visibleCards = cards.slice(0, 5);
  const centerIndex = Math.floor(visibleCards.length / 2);

  visibleCards[centerIndex]?.classList.add("is-featured");

  if (visibleCards.length >= 5) {
    visibleCards[0].classList.add("is-muted");
    visibleCards[4].classList.add("is-muted");
  }
};

let isCarouselAnimating = false;

const ensureProductCarouselItems = () => {
  if (!productTrack) {
    return;
  }

  while (productCards().length > 0 && productCards().length < 5) {
    const cards = productCards();
    const clone = cards[cards.length % cards.length].cloneNode(true);

    productTrack.append(clone);
  }
};

const rotateProductCarousel = (direction) => {
  if (!productTrack || isCarouselAnimating) {
    return;
  }

  const cards = productCards();

  if (cards.length < 2) {
    return;
  }

  const firstRects = new Map(cards.map((card) => [card, card.getBoundingClientRect()]));

  isCarouselAnimating = true;
  productTrack.classList.add("is-carousel-animating");

  if (direction > 0) {
    productTrack.append(cards[0]);
  } else {
    productTrack.prepend(cards[cards.length - 1]);
  }

  productTrack.getBoundingClientRect();
  syncProductCarousel();
  syncFavoriteButtons();

  productCards().forEach((card) => {
    const firstRect = firstRects.get(card);

    if (!firstRect) {
      return;
    }

    const lastRect = card.getBoundingClientRect();

    card.style.setProperty("--slide-x", `${firstRect.left - lastRect.left}px`);
    card.style.setProperty("--slide-y", `${firstRect.top - lastRect.top}px`);
  });

  productTrack.getBoundingClientRect();

  window.requestAnimationFrame(() => {
    productCards().forEach((card) => {
      card.style.setProperty("--slide-x", "0px");
      card.style.setProperty("--slide-y", "0px");
    });
  });

  window.setTimeout(() => {
    productCards().forEach((card) => {
      card.style.removeProperty("--slide-x");
      card.style.removeProperty("--slide-y");
    });

    productTrack.classList.remove("is-carousel-animating");
    isCarouselAnimating = false;
  }, 440);
};

const isProductTrackScrollable = () => productTrack && productTrack.scrollWidth > productTrack.clientWidth + 4;

ensureProductCarouselItems();
syncProductCarousel();

menuToggle?.addEventListener("click", () => {
  const isOpen = body.classList.toggle("menu-open");
  menuToggle.setAttribute("aria-label", isOpen ? "Close menu" : "Open menu");
});

nav?.addEventListener("click", (event) => {
  if (event.target.closest("a")) {
    closeMenu();
  }
});

cartOpenButtons.forEach((button) => button.addEventListener("click", openCart));
cartCloseButtons.forEach((button) => button.addEventListener("click", closeCart));
cartOverlay?.addEventListener("click", closeCart);

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    closeMenu();
    closeCart();
  }
});

document.querySelectorAll("[data-add-cart]").forEach((button) => {
  button.addEventListener("click", async () => {
    button.disabled = true;

    try {
      const data = await apiPost("/cart/add", {
        productId: button.dataset.productId,
      });

      hideCartMessage();
      renderCart(data.cart);
      openCart();
    } finally {
      button.disabled = false;
    }
  });
});

cartItems?.addEventListener("click", async (event) => {
  const removeButton = event.target.closest("[data-remove-cart]");

  if (!removeButton) {
    return;
  }

  removeButton.disabled = true;

  try {
    const data = await apiPost("/cart/remove", {
      productId: removeButton.dataset.removeCart,
    });

    renderCart(data.cart);
  } finally {
    removeButton.disabled = false;
  }
});

checkoutButton?.addEventListener("click", async () => {
  checkoutButton.disabled = true;

  try {
    const data = await apiPost("/checkout");

    renderCart(data.cart);
    const storage = data.order.storage === "session" ? " Saved in session." : "";
    showCartMessage(`Order #${data.order.id} has been created.${storage}`);
  } catch (error) {
    showCartMessage(error.message, true);
  } finally {
    checkoutButton.disabled = state.cart.count === 0;
  }
});

document.querySelectorAll("[data-favorite-toggle]").forEach((button) => {
  button.addEventListener("click", async () => {
    button.disabled = true;

    try {
      const data = await apiPost("/favorites/toggle", {
        productId: button.dataset.productId,
      });

      state.favorites = data.favorites;
      syncFavoriteButtons();
    } finally {
      button.disabled = false;
    }
  });
});

document.querySelectorAll("[data-slide]").forEach((button) => {
  button.addEventListener("click", () => {
    const direction = button.dataset.slide === "next" ? 1 : -1;

    rotateProductCarousel(direction);
  });
});

if (productTrack) {
  let isDragging = false;
  let startX = 0;
  let startScroll = 0;
  let hasDragged = false;
  let dragDelta = 0;

  productTrack.addEventListener("pointerdown", (event) => {
    if (event.button !== 0 || event.target.closest("button, a")) {
      return;
    }

    isDragging = true;
    hasDragged = false;
    dragDelta = 0;
    startX = event.clientX;
    startScroll = productTrack.scrollLeft;
    productTrack.classList.add("is-dragging");
    productTrack.setPointerCapture(event.pointerId);
  });

  productTrack.addEventListener("pointermove", (event) => {
    if (!isDragging) {
      return;
    }

    const delta = event.clientX - startX;
    dragDelta = delta;

    if (Math.abs(delta) > 4) {
      hasDragged = true;
    }

    if (isProductTrackScrollable()) {
      productTrack.scrollLeft = startScroll - delta;
    }
  });

  const stopDragging = (event) => {
    if (!isDragging) {
      return;
    }

    isDragging = false;
    productTrack.classList.remove("is-dragging");

    if (productTrack.hasPointerCapture(event.pointerId)) {
      productTrack.releasePointerCapture(event.pointerId);
    }

    if (!isProductTrackScrollable() && Math.abs(dragDelta) > 48) {
      rotateProductCarousel(dragDelta < 0 ? 1 : -1);
    }
  };

  productTrack.addEventListener("pointerup", stopDragging);
  productTrack.addEventListener("pointercancel", stopDragging);
  productTrack.addEventListener(
    "click",
    (event) => {
      if (!hasDragged) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      hasDragged = false;
    },
    true
  );
}

renderCart(state.cart);
syncFavoriteButtons();
