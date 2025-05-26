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
  .home-container p {
    font-size: 1.3rem;
    color: #ddd;
    margin-bottom: 3rem;
    text-shadow: 0 1px 5px rgba(0,0,0,0.6);
  }

  /* Footer */
  footer#footer {
    background: rgba(30, 20, 114, 0.9);
    color: #eee;
    padding: 3rem 1rem 1rem;
    margin-top: 5rem;
  }
  .footer-container {
    max-width: 900px;
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
    font-weight: 700;
    text-shadow: 0 1px 3px rgba(0,0,0,0.7);
  }
  .footer-section p {
    color: #ddd;
    font-size: 0.95rem;
    margin-bottom: 0.4rem;
    text-shadow: 0 1px 2px rgba(0,0,0,0.6);
  }
  .footer-bottom {
    text-align: center;
    border-top: 1px solid rgba(255 255 255 / 0.2);
    margin-top: 1.5rem;
    padding-top: 0.75rem;
    font-size: 0.9rem;
    color: #bbb;
    text-shadow: 0 1px 1px rgba(0,0,0,0.7);
  }

  /* Responsive */
  @media (max-width: 650px) {
    nav.navbar ul {
      flex-direction: column;
      gap: 1rem;
    }
    .footer-container {
      flex-direction: column;
      gap: 1.5rem;
    }
  }
</style>
</head>
<body>
  <nav class="navbar" role="navigation">
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
    <h2>Welcome to Tirana Unplugged !</h2>
    <h3>Create your FREE event website today<h3/>
  </main>

  <footer id="footer" role="contentinfo">
    <div class="footer-container">
      <section class="footer-section" aria-labelledby="footer-title">
        <h2 id="footer-title">How it Works</h2>
        <p>Tirana Unplugged is an online platform that allows users to book and create events in the city of Tirana. 
Users can explore various event opportunities and organize their favorite activities..</p>
      </section>

      <section class="footer-section" aria-labelledby="footer-contact">
        <h3 id="footer-contact">Contact</h3>
        <p>+355 69 558 6969</p>
        <p>TiranaUnplugged@gmail.com</p>
        <p>Rruga e Elbasanit, Tirana, Albania</p>
      </section>

      <section class="footer-section" aria-labelledby="footer-social">
        <h3 id="footer-social">Social Media</h3>
        <p>TikTok</p>
        <p>Instagram</p>
        <p>Facebook</p>
      </section>
    </div>

    <div class="footer-bottom">
      <p>Copyright 2025. All rights reserved.</p>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const aboutUsLink = document.querySelector('a[href="#footer"]');
      if (aboutUsLink) {
        aboutUsLink.addEventListener('click', (e) => {
          e.preventDefault();
          document.getElementById('footer').scrollIntoView({behavior: 'smooth'});
        });
      }
    });
  </script>
</body>
</html>


