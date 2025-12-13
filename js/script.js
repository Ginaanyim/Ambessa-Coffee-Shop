//vänta tills DOM:en är laddad innan javacripten börjar köras
document.addEventListener('DOMContentLoaded', function () {

  //Hanterar toggling av mobilmenyn och byter ikon beroende på om menyn är stängd eller öppen
  const menuToggle = document.querySelector('.menu-toggle');
  const navbar = document.querySelector('.navbar');
  if (menuToggle && navbar) {
    menuToggle.addEventListener('click', function () {
      navbar.classList.toggle('active');
      menuToggle.textContent = navbar.classList.contains('active') ? '✕' : '☰';
    });
  }

  //När anvndaren klickar på add to cart knappen läggs produkter i kundvagnen
  document.querySelectorAll('.cart-btn').forEach(button => {
    button.addEventListener('click', () => {
      const productId = button.getAttribute('data-product-id');
      let quantity = 1;
      const quantityInput = document.getElementById('quantity');
      if (quantityInput) {
        quantity = parseInt(quantityInput.value) || 1;
      }

      if (productId) {
        addToCart(productId, quantity);
      } else {
        alert('No product chosen.');
      }
    });
  });

  //Hantering för back-knapp på produktsidan och återvänder till den tidigare lästa sidan
  const backBtn = document.getElementById("backBtn");
  if (backBtn) {
    backBtn.addEventListener("click", function () {
      history.back();
    });
  }
});

//Lägger till en produkt i kundvagnen
function addToCart(productId, quantity = 1) {
  //Skapar data som sedan ska skickas till servern, förbereder servern, produktens id och antal 
  const formData = new URLSearchParams();
  formData.append('action', 'add');       
  formData.append('product_id', productId); 
  formData.append('quantity', quantity);   

  //Skicar data till servern med fetch, metoden post, talar om vilken typ av data samt data som skickas som text
  fetch('/php/cart_handler.php', {
    method: 'POST', 
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: formData.toString() 
  })
  .then(function(res) {
    //Konverterar serverns svar till JavaScript-objekt från JSON 
    return res.json();
  })
  .then(function(data) {
    //Om produkten lades till via servern visas mini-cart om något gått fel visa meddelande
    if (data.success) {
      showMiniCart();
    } else {
      alert("Something went wrong: " + (data.message || "Unknown error."));
    }
  })
  .catch(function(error) {
    //Om fetch misslyckas loggas felet i konsolen
    console.error("Error:", error);
  });
}

//Funktionen visar mini-cart på sidan, hämtar aktuella kundvagnen från ervern via fetch och konverterar json svaret till javascript-objekt
function showMiniCart() {
  // KORRIGERING: Använder nu den korrekta sökvägen (utan /uppgifter/9/)
  fetch('/php/cart-status.php')
  .then(function(res) {
    //Kontrollerar att svaret är OK innan JSON parsas
    if (!res.ok) {
        throw new Error('Network response was not ok: ' + res.statusText);
    }
    return res.json();
  })
  .then(function(cart) {
    const miniCart = document.getElementById('mini-cart'); 
    const cartItemsContainer = document.getElementById('cart-items'); 
    const cartTotalContainer = document.getElementById('cart-total'); 
    const template = document.getElementById('mini-cart-item-template'); 

    //Om något element saknas gör ingenting
    if (!miniCart || !cartItemsContainer || !template) {
        console.warn("Mini-cart HTML elements are missing on the page.");
        return;
    }

    //Tömmer tidigare innehåll
    cartItemsContainer.innerHTML = '';
    cartTotalContainer.innerHTML = '';

    //Om kundvagnen innehåller produkter skapas total variabeln för att räkna ihop totalpriset
    if (cart.items && cart.items.length > 0) {
      let total = 0;

      //Kopierar varje produkt i kundvagnen från mallen för att visa den
      cart.items.forEach(function(item) {
        const clone = template.content.cloneNode(true); 
        let html = clone.querySelector('.mini-cart-item').innerHTML;

        //Fyller i produktens namn, antal, pris och bild, lägger till produkten i mini-cart + uppdaterat totalpris
        html = html
          .replace(/---name---/g, item.name)
          .replace(/---quantity---/g, item.quantity)
          .replace(/---price---/g, item.price)
          // KORRIGERAD SÖKVÄG: Denna sökväg var redan rätt i originalkoden, den pekar på /images/ i roten
          .replace(/---image---/g, '/images/' + item.image); 

        clone.querySelector('.mini-cart-item').innerHTML = html;
        cartItemsContainer.appendChild(clone);
        total += item.price * item.quantity; 
      });
      
      //Visa totalpris annaras meddelande
      cartTotalContainer.innerHTML = 'Total: ' + total + ' kr'; 
    } else {
      cartItemsContainer.innerHTML = 'Cart is empty.'; 
      cartTotalContainer.innerHTML = 'Total: 0 kr'; 
    }

    //Visar mini-carten genom att lägga till active klassen
    miniCart.classList.add("active");

    //Ta bort active efter 5 sekunder så mini-carten intr visas hela tiden
    setTimeout(function() {
      miniCart.classList.remove("active");
    }, 5000);
  })
  .catch(function(err) {
    //Visar fel om det inte gick att hämta kundvagnen
    console.error("Error loading cart status:", err);
    console.warn("Couldn't load cart. This is often due to missing HTML elements or a server 500 error after 404 was fixed.");
  });
}