/**
 * Pawfect Pet Shop - Main JavaScript File
 * Contains common functionality used throughout the application
 */

// Global variables
let cartCount = 0

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  initializeApp()
})

/**
 * Initialize the application
 */
function initializeApp() {
  initializeSearch()
  initializeCartCount()
  initializeAlerts()
  initializeTooltips()
  initializeModals()
  initializeFormValidation()
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
  const searchInput = document.getElementById("search-input")
  const searchResults = document.getElementById("search-results")

  if (!searchInput || !searchResults) return

  let searchTimeout

  searchInput.addEventListener("input", function () {
    const query = this.value.trim()

    clearTimeout(searchTimeout)

    if (query.length < 2) {
      searchResults.style.display = "none"
      return
    }

    searchTimeout = setTimeout(() => {
      performSearch(query)
    }, 300)
  })

  // Hide search results when clicking outside
  document.addEventListener("click", (e) => {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
      searchResults.style.display = "none"
    }
  })
}

/**
 * Perform search and display results
 */
function performSearch(query) {
  const searchResults = document.getElementById("search-results")

  fetch(`/search?q=${encodeURIComponent(query)}`)
    .then((response) => response.json())
    .then((data) => {
      displaySearchResults(data.results)
    })
    .catch((error) => {
      console.error("Search error:", error)
      searchResults.style.display = "none"
    })
}

/**
 * Display search results
 */
function displaySearchResults(results) {
  const searchResults = document.getElementById("search-results")

  if (results.length === 0) {
    searchResults.innerHTML = '<div class="p-3 text-muted">No results found</div>'
    searchResults.style.display = "block"
    return
  }

  let html = ""
  results.forEach((result) => {
    html += `
            <div class="search-result-item">
                <div class="d-flex align-items-center">
                    <img src="${result.image}" alt="${result.title}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">
                            <a href="${result.url}" class="text-decoration-none">${result.title}</a>
                        </h6>
                        <p class="mb-1 small text-muted">${result.description}</p>
                        <small class="text-primary fw-bold">${result.price}</small>
                    </div>
                    <span class="badge bg-secondary">${result.type}</span>
                </div>
            </div>
        `
  })

  searchResults.innerHTML = html
  searchResults.style.display = "block"
}

/**
 * Initialize cart count
 */
function initializeCartCount() {
  updateCartCount()
}

/**
 * Update cart count display
 */
function updateCartCount() {
  fetch("/cart/count")
    .then((response) => response.json())
    .then((data) => {
      const cartCountElement = document.getElementById("cart-count")
      if (cartCountElement) {
        cartCount = data.count
        cartCountElement.textContent = cartCount
        cartCountElement.style.display = cartCount > 0 ? "inline" : "none"
      }
    })
    .catch((error) => {
      console.error("Error updating cart count:", error)
    })
}

/**
 * Initialize alert system
 */
function initializeAlerts() {
  // Auto-hide alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert:not(.alert-permanent)")
  alerts.forEach((alert) => {
    setTimeout(() => {
      if (alert.parentNode) {
        alert.style.opacity = "0"
        setTimeout(() => {
          if (alert.parentNode) {
            alert.remove()
          }
        }, 300)
      }
    }, 5000)
  })
}

/**
 * Show alert message
 */
function showAlert(message, type = "info", duration = 5000) {
  const alertContainer = document.getElementById("alert-container")
  if (!alertContainer) return

  const alertId = "alert-" + Date.now()
  const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${getAlertIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `

  alertContainer.insertAdjacentHTML("beforeend", alertHtml)

  // Auto-hide after duration
  if (duration > 0) {
    setTimeout(() => {
      const alert = document.getElementById(alertId)
      if (alert) {
        alert.style.opacity = "0"
        setTimeout(() => {
          if (alert.parentNode) {
            alert.remove()
          }
        }, 300)
      }
    }, duration)
  }
}

/**
 * Get icon for alert type
 */
function getAlertIcon(type) {
  const icons = {
    success: "check-circle",
    error: "exclamation-triangle",
    danger: "exclamation-triangle",
    warning: "exclamation-triangle",
    info: "info-circle",
  }
  return icons[type] || "info-circle"
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))
}

/**
 * Initialize modals
 */
function initializeModals() {
  // Add any modal-specific initialization here
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
  const forms = document.querySelectorAll(".needs-validation")

  forms.forEach((form) => {
    form.addEventListener("submit", (event) => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add("was-validated")
    })
  })
}

/**
 * Add product to cart
 */
function addToCart(productId, quantity = 1) {
  const formData = new FormData()
  formData.append("product_id", productId)
  formData.append("quantity", quantity)
  formData.append("_token", getCSRFToken())

  return fetch("/cart/add", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        updateCartCount()
        showAlert(data.message, "success")
      } else {
        showAlert(data.message, "error")
      }
      return data
    })
    .catch((error) => {
      console.error("Error adding to cart:", error)
      showAlert("Failed to add product to cart", "error")
      throw error
    })
}

/**
 * Update cart item quantity
 */
function updateCartQuantity(cartItemId, quantity) {
  const formData = new FormData()
  formData.append("cart_item_id", cartItemId)
  formData.append("quantity", quantity)
  formData.append("_token", getCSRFToken())

  return fetch("/cart/update", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        updateCartCount()
        showAlert(data.message, "success")
      } else {
        showAlert(data.message, "error")
      }
      return data
    })
    .catch((error) => {
      console.error("Error updating cart:", error)
      showAlert("Failed to update cart", "error")
      throw error
    })
}

/**
 * Get CSRF token
 */
function getCSRFToken() {
  const tokenElement = document.querySelector('meta[name="csrf-token"]')
  if (tokenElement) {
    return tokenElement.getAttribute("content")
  }

  // Fallback: try to get from a hidden input
  const hiddenInput = document.querySelector('input[name="_token"]')
  if (hiddenInput) {
    return hiddenInput.value
  }

  // Generate a simple token if none exists (for development)
  return Math.random().toString(36).substr(2, 9)
}

/**
 * Format currency
 */
function formatCurrency(amount, currency = "USD") {
  const symbols = {
    USD: "$",
    EUR: "€",
    GBP: "£",
    JPY: "¥",
  }

  const symbol = symbols[currency] || "$"
  return symbol + Number.parseFloat(amount).toFixed(2)
}

/**
 * Debounce function
 */
function debounce(func, wait, immediate) {
  let timeout
  return function executedFunction() {
    const args = arguments
    const later = () => {
      timeout = null
      if (!immediate) func.apply(this, args)
    }
    const callNow = immediate && !timeout
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
    if (callNow) func.apply(this, args)
  }
}

/**
 * Smooth scroll to element
 */
function scrollToElement(elementId, offset = 0) {
  const element = document.getElementById(elementId)
  if (element) {
    const elementPosition = element.offsetTop - offset
    window.scrollTo({
      top: elementPosition,
      behavior: "smooth",
    })
  }
}

/**
 * Copy text to clipboard
 */
function copyToClipboard(text) {
  if (navigator.clipboard) {
    navigator.clipboard
      .writeText(text)
      .then(() => {
        showAlert("Copied to clipboard!", "success", 2000)
      })
      .catch((err) => {
        console.error("Failed to copy: ", err)
        showAlert("Failed to copy to clipboard", "error")
      })
  } else {
    // Fallback for older browsers
    const textArea = document.createElement("textarea")
    textArea.value = text
    document.body.appendChild(textArea)
    textArea.focus()
    textArea.select()
    try {
      document.execCommand("copy")
      showAlert("Copied to clipboard!", "success", 2000)
    } catch (err) {
      console.error("Failed to copy: ", err)
      showAlert("Failed to copy to clipboard", "error")
    }
    document.body.removeChild(textArea)
  }
}

/**
 * Validate email format
 */
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Validate phone number format
 */
function isValidPhone(phone) {
  const phoneRegex = /^[+]?[1-9][\d]{0,15}$/
  return phoneRegex.test(phone.replace(/[\s\-$$$$]/g, ""))
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
  if (bytes === 0) return "0 Bytes"

  const k = 1024
  const sizes = ["Bytes", "KB", "MB", "GB"]
  const i = Math.floor(Math.log(bytes) / Math.log(k))

  return Number.parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
}

/**
 * Get URL parameter
 */
function getUrlParameter(name) {
  name = name.replace(/[[]/, "\\[").replace(/[\]]/, "\\]")
  const regex = new RegExp("[\\?&]" + name + "=([^&#]*)")
  const results = regex.exec(location.search)
  return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "))
}

/**
 * Set URL parameter
 */
function setUrlParameter(name, value) {
  const url = new URL(window.location)
  url.searchParams.set(name, value)
  window.history.pushState({}, "", url)
}

/**
 * Remove URL parameter
 */
function removeUrlParameter(name) {
  const url = new URL(window.location)
  url.searchParams.delete(name)
  window.history.pushState({}, "", url)
}

// Export functions for use in other scripts
window.PawfectApp = {
  showAlert,
  addToCart,
  updateCartQuantity,
  updateCartCount,
  formatCurrency,
  copyToClipboard,
  isValidEmail,
  isValidPhone,
  formatFileSize,
  getUrlParameter,
  setUrlParameter,
  removeUrlParameter,
  scrollToElement,
  debounce,
}
