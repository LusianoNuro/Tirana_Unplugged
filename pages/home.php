<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Home - Event Management</title>
<style>
  /* Reset and base */
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }
  html, body {
    height: 100%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #eee;
    line-height: 1.6;
    background: url('https://images.squarespace-cdn.com/content/v1/57b9b98a29687f1ef5c622df/1481560170009-3A9N8U2LZA770OJAS6W5/0000000.jpg') no-repeat center center fixed;
    background-size: cover;
  }
  /* Overlay for better text readability */
  body::before {
    content: "";
    position: fixed;
    top:0; left:0; right:0; bottom:0;
    background: rgba(0,0,0,0.55);
    z-index: -1;
  }
  /* Navbar */
  nav.navbar {
    background: rgba(30, 60, 114, 0.85); /* translucent deep navy */
    box-shadow: 0 4px 10px rgba(0,0,0,0.6);
    padding: 1rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
  }
  nav.navbar ul {
    list-style: none;
    display: flex;
    justify-content: center;
    gap: 2rem;
  }
  nav.navbar a {
    color: #fbc531;
    font-weight: 600;
    text-transform: uppercase;
    text-decoration: none;
    letter-spacing: 0.08em;
    font-size: 0.9rem;
    transition: color 0.3s, border-bottom 0.3s;
    padding-bottom: 0.25rem;
  }
  nav.navbar a:hover,
  nav.navbar a:focus {
    color: #fff;
    border-bottom: 2px solid #fff;
    outline: none;
  }

  /* Hero Section */
  .home-container {
    max-width: 900px;
    margin: 6rem auto 3rem;
    background: rgba(0, 0, 0, 0.65);
    border-radius: 12px;
    padding: 3rem 2rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.8);
    text-align: center;
  }
  .home-container h2 {
    font-size: 3rem;
    color: #fbc531;
    margin-bottom: 0.75rem;
    font-weight: 800;
    text-shadow: 0 2px 7px rgba(0,0,0,0.6);
  }
  .home-container h3 {
    font-size: 1.1rem;
    color: #fff;
    margin-bottom: 1.5rem;
    font-weight: 600;
    text-shadow: 0 1px 5px rgba(0,0,0,0.6);
  }
  .home-container p {
    font-size: 1.3rem;
    color: #ddd;
    margin-bottom: 3rem;
    text-shadow: 0 1px 5px rgba(0,0,0,0.6);
  }

  /* Call to Action Button */
  .home-hero-cta {
    margin: 2rem 0 2.5rem;
  }
  .cta-btn {
    background: linear-gradient(90deg, #fbc531 60%, #1e3c72 100%);
    color: #222;
    font-weight: bold;
    font-size: 1.2rem;
    padding: 0.9rem 2.2rem;
    border-radius: 30px;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(251,197,49,0.18);
    transition: background 0.3s, color 0.3s, transform 0.2s;
    border: none;
    display: inline-block;
  }
  .cta-btn:hover {
    background: linear-gradient(90deg, #1e3c72 60%, #fbc531 100%);
    color: #fff;
    transform: translateY(-2px) scale(1.04);
  }

  /* Features Section */
  .home-features {
    display: flex;
    justify-content: center;
    gap: 2.5rem;
    margin-top: 2.5rem;
    flex-wrap: wrap;
  }
  .feature-card {
    background: rgba(30,60,114,0.85);
    border-radius: 14px;
    padding: 2rem 1.5rem 1.5rem;
    box-shadow: 0 4px 18px rgba(0,0,0,0.18);
    text-align: center;
    width: 240px;
    min-height: 270px;
    color: #eee;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .feature-card:hover {
    transform: translateY(-6px) scale(1.04);
    box-shadow: 0 8px 32px rgba(251,197,49,0.18);
  }
  .feature-card img {
    width: 56px;
    height: 56px;
    margin-bottom: 1rem;
    filter: drop-shadow(0 2px 8px #fbc53144);
  }
  .feature-card h4 {
    color: #fbc531;
    margin-bottom: 0.5rem;
    font-size: 1.25rem;
    font-weight: 700;
  }
  .feature-card p {
    color: #eee;
    font-size: 1rem;
  }

  /* Inspirational Quote Section */
  .home-inspiration {
    margin: 2.5rem auto 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2.2rem;
    max-width: 700px;
    background: rgba(30,60,114,0.7);
    border-radius: 18px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.18);
    padding: 1.5rem 2rem;
  }
  .inspiration-img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 14px;
    box-shadow: 0 2px 10px #fbc53144;
    border: 3px solid #fbc531;
  }
  .inspiration-quote {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: #fff;
    font-size: 1.1rem;
    font-style: italic;
    font-weight: 500;
  }
  .quote-icon {
    font-size: 2rem;
    color: #fbc531;
    margin-right: 0.5rem;
  }

  /* Sponsors Section */
  .home-sponsors {
    margin: 3.5rem auto 0;
    text-align: center;
    background: linear-gradient(90deg, #1e3c72 0%, #fbc531 100%);
    border-radius: 14px;
    padding: 1.5rem 1rem 2rem;
    max-width: 700px;
    box-shadow: 0 2px 18px 0 rgba(251,197,49,0.18), 0 4px 32px 0 rgba(30,60,114,0.10);
  }
  .home-sponsors h4 {
    color: #fbc531;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 1.2rem;
    letter-spacing: 0.04em;
  }
  .sponsor-logos {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2.2rem;
    flex-wrap: wrap;
  }
  .sponsor-logos img {
    height: 90px;
    width: auto;
    background: #fff;
    border-radius: 8px;
    padding: 0.4rem 0.8rem;
    box-shadow: 0 2px 8px #fbc53133;
    transition: transform 0.2s, box-shadow 0.2s;
    max-width: 200px;
  }
  .sponsor-logos img[alt="Red Bull Logo"] {
    background: none;
    border: none;
    padding: 0;
  }
  .sponsor-logos img:hover {
    transform: scale(1.08) rotate(-2deg);
    box-shadow: 0 4px 18px #fbc53155;
  }
  @media (max-width: 700px) {
      .sponsor-logos {
        gap: 1.1rem;
      }
      .home-sponsors {
        padding: 1rem 0.2rem 1.5rem;
      }
    }

  /* Footer styles from create_event.php */
  footer#footer {
      background: rgba(30, 60, 114, 0.9);
      color: #eee;
      padding: 2rem 1rem;
      margin-top: 4rem;
  }
  .footer-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 2rem;
  }
  .footer-section {
      flex: 1 1 250px;
      min-width: 220px;
  }
  .footer-section h2, .footer-section h3 {
      color: #fbc531;
      margin-bottom: 0.75rem;
  }
  .footer-bottom {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.9rem;
      color: #bbb;
  }

  /* Live Event Banner */
  .live-event-banner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    background: linear-gradient(90deg, #f857a6 0%, #ff5858 100%);
    color: #fff;
    font-size: 1.15rem;
    font-weight: 600;
    border-radius: 12px;
    box-shadow: 0 2px 18px #f857a633;
    padding: 1.1rem 2rem 1.1rem 1.2rem;
    margin: 2.5rem auto 2.5rem;
    max-width: 700px;
    animation: pulseLive 2s infinite;
    position: fixed;
    left: 50%;
    top: 60px;
    transform: translateX(-50%);
    z-index: 9999;
    max-width: 95vw;
    box-shadow: 0 8px 32px #ff5858cc;
    margin: 0;
    display: flex;
  }
  .close-popup {
    background: none;
    border: none;
    color: #fff;
    font-size: 2rem;
    font-weight: bold;
    margin-left: 1.2rem;
    cursor: pointer;
    transition: color 0.2s;
    line-height: 1;
  }
  .close-popup:hover {
    color: #fbc531;
  }
  @media (max-width: 700px) {
      .live-event-banner {
        flex-direction: column;
        align-items: flex-start;
        padding: 1rem 0.7rem 1rem 1rem;
        font-size: 1rem;
      }
      .close-popup {
        align-self: flex-end;
        margin: 0 0 0.5rem 0;
      }
    }
  @keyframes blinkLive {
    0% { opacity: 1; }
    100% { opacity: 0.4; }
  }
  @keyframes pulseLive {
    0% { box-shadow: 0 2px 18px #f857a633; }
    50% { box-shadow: 0 4px 32px #ff5858aa; }
    100% { box-shadow: 0 2px 18px #f857a633; }
  }

  /* Responsive */
  @media (max-width: 650px) {
    nav.navbar {
      padding: 0.7rem 0.5rem;
    }
    nav.navbar ul {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
      background: rgba(30, 60, 114, 0.97);
      border-radius: 0 0 12px 12px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.18);
      padding: 0.5rem 1rem;
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      display: none;
      z-index: 200;
    }
    nav.navbar ul.active {
      display: flex;
    }
    nav.navbar .navbar-toggle {
      display: block;
      background: none;
      border: none;
      color: #fbc531;
      font-size: 2rem;
      position: absolute;
      right: 1.2rem;
      top: 1rem;
      z-index: 201;
      cursor: pointer;
    }
    nav.navbar ul li {
      width: 100%;
    }
    nav.navbar ul li a {
      display: block;
      width: 100%;
      padding: 0.7rem 0;
      border-bottom: 1px solid #fbc53122;
    }
    nav.navbar ul li:last-child a {
      border-bottom: none;
    }
  }
  @media (max-width: 900px) {
      .home-features {
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
      }
    }
  @media (max-width: 700px) {
      .home-inspiration {
        flex-direction: column;
        gap: 1.2rem;
        padding: 1.2rem 0.5rem;
      }
      .inspiration-img {
        width: 90px;
        height: 90px;
      }
    }
  /* Responsive Navbar */
  @media (max-width: 700px) {
    nav.navbar {
      padding: 0.7rem 0.5rem;
    }
    nav.navbar ul {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
      background: rgba(30, 60, 114, 0.97);
      border-radius: 0 0 12px 12px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.18);
      padding: 0.5rem 1rem;
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      display: none;
      z-index: 200;
    }
    nav.navbar ul.active {
      display: flex;
    }
    nav.navbar .navbar-toggle {
      display: block;
      background: none;
      border: none;
      color: #fbc531;
      font-size: 2rem;
      position: absolute;
      right: 1.2rem;
      top: 1rem;
      z-index: 201;
      cursor: pointer;
    }
  }
  @media (min-width: 701px) {
    nav.navbar .navbar-toggle {
      display: none;
    }
    nav.navbar ul {
      flex-direction: row;
      position: static;
      background: none;
      box-shadow: none;
      padding: 0;
      display: flex !important;
    }
  }
</style>
</head>
<body>
  <nav class="navbar" role="navigation">
    <button class="navbar-toggle" aria-label="Toggle navigation">&#9776;</button>
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="events.php">Events</a></li>
      <li><a href="create_event.php">Create Event</a></li>
      <li><a href="reservations.php">My Reservations</a></li>
      <li><a href="support.php">Support</a></li>
      <li><a href="#footer">About Us</a></li>
      <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
  </nav>

  <main class="home-container" role="main" aria-label="Welcome and introduction">
    <h2>Welcome to Tirana Unplugged, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
    <h3>Create your FREE event website today</h3>
    <div class="home-hero-cta">
      <a href="create_event.php" class="cta-btn">+ Create Your First Event</a>
    </div>
    <div class="home-features">
      <div class="feature-card">
        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828884.png" alt="Easy Booking" />
        <h4>Easy Booking</h4>
        <p>Reserve your spot at the hottest events in Tirana with just a click.</p>
      </div>
      <div class="feature-card">
        <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" alt="Host Events" />
        <h4>Host Events</h4>
        <p>Create and manage your own events, from concerts to workshops.</p>
      </div>
      <div class="feature-card">
        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828919.png" alt="Community" />
        <h4>Community</h4>
        <p>Connect with fellow event lovers and grow your network.</p>
      </div>
    </div>

    <!-- Creative: Live Upcoming Event Banner (as Ad Popup) -->
    <div class="live-event-banner" id="liveEventPopup" style="display:none;">
      <span class="live-dot"></span>
      <span class="live-text">Adds: Summer Music Fest 2025 is happening now in Tirana! <a href="events.php" class="live-link">See details &rarr;</a></span>
      <button class="close-popup" id="closeLivePopup" aria-label="Close">&times;</button>
    </div>

    <div class="home-inspiration">
      <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80" alt="Tirana Event Inspiration" class="inspiration-img" />
      <div class="inspiration-quote">
        <span class="quote-icon">&#10024;</span>
        <p>"The best memories are made at events. Start creating yours today!"</p>
      </div>
    </div>

    <div class="home-sponsors">
      <h4>Our Proud Sponsors</h4>
      <div class="sponsor-logos">
        <a href="https://www.redbull.com/" target="_blank" title="Red Bull"><img src="https://assets.turbologo.com/blog/en/2019/12/19084742/Red-Bull-Logo-evolution.png" alt="Red Bull Logo"></a>
        <a href="https://www.vodafone.com/" target="_blank" title="Vodafone"><img src="https://1000logos.net/wp-content/uploads/2017/06/Vodafone-logo.jpg" alt="Vodafone Logo"></a>
        <a href="https://www.coca-cola.com/" target="_blank" title="Coca-Cola"><img src="https://brandlogos.net/wp-content/uploads/2022/10/coca-cola-logo_brandlogos.net_8kh4z.png" alt="Coca-Cola Logo"></a>
        <a href="https://www.heineken.com/" target="_blank" title="Heineken"><img src="https://brandlogos.net/wp-content/uploads/2022/03/heineken_beer-logo-brandlogos.net_.png" alt="Heineken Logo"></a>
      </div>
    </div>
  </main>

  <footer id="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h2>Tirana Unplugged</h2>
            <p>Tirana Unplugged is an online platform that enables users to book and create events in the city of Tirana. With a simple and intuitive design, users can explore opportunities for various events and organize their favorite activities.</p>
        </div>
        <div class="footer-section">
            <h3>Contact</h3>
            <p><a href="tel:+355695586969" style="color: #fbc531; text-decoration: underline;">+355 69 558 6969</a></p>
            <p><a href="mailto:tiranaUnplugged@gmail.com" style="color: #fbc531; text-decoration: underline;">tiranaUnplugged@gmail.com</a></p>
            <p>Elbasani Street, Tirana, Albania</p>
        </div>
        <div class="footer-section">
            <h3>Social Media</h3>
            <p>TikTok</p>
            <p>Instagram</p>
            <p>Facebook</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>Copyright 2025. All rights reserved.</p>
    </div>
</footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Navbar toggle for mobile
      const toggle = document.querySelector('.navbar-toggle');
      const navUl = document.querySelector('nav.navbar ul');
      if (toggle && navUl) {
        toggle.addEventListener('click', () => {
          navUl.classList.toggle('active');
        });
      }
      // Smooth scroll for About Us
      const aboutUsLink = document.querySelector('a[href="#footer"]');
      if (aboutUsLink) {
        aboutUsLink.addEventListener('click', (e) => {
          e.preventDefault();
          document.getElementById('footer').scrollIntoView({behavior: 'smooth'});
        });
      }
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const popup = document.getElementById('liveEventPopup');
      const closeBtn = document.getElementById('closeLivePopup');
      let popupTimeout = null;
      let reappearTimeout = null;

      function showPopup() {
        popup.style.display = 'flex';
      }
      function hidePopup() {
        popup.style.display = 'none';
      }

      // Show popup 5 seconds after login/page load
      popupTimeout = setTimeout(showPopup, 5000);

      closeBtn.onclick = function() {
        hidePopup();
        // Show again after 30 seconds if closed
        if (reappearTimeout) clearTimeout(reappearTimeout);
        reappearTimeout = setTimeout(showPopup, 5000);
      };
    });
  </script>
</body>
</html>


