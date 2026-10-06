    <footer class="footer">
        <div class="footer-content">
            <div class="footer-brand-block">
                <a href="index.php" class="brand-logo" style="justify-content: center;">
                    <img src="assets/logo.jpeg" alt="Samprime Autos Global" class="brand-logo-img"> SAMPRIME <span>AUTOS</span>
                </a>
                <p class="footer-tagline">Your trusted luxury & verified automobile partner.</p>
            </div>
            
            <div class="footer-socials">
                <a href="<?php echo htmlspecialchars($whatsapp_link, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="social-link whatsapp" title="Chat on WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
                <a href="<?php echo htmlspecialchars($instagram_link, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="social-link instagram" title="Follow on Instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="<?php echo htmlspecialchars($x_link, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="social-link x-twitter" title="Follow on X">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <a href="mailto:Samprimeglobalenterprises@gmail.com" target="_blank" rel="noopener noreferrer" class="social-link email" title="Send Email: Samprimeglobalenterprises@gmail.com">
                    <i class="fa-solid fa-envelope"></i>
                </a>
            </div>

            <div class="footer-contact-block">
                <div class="phone-pills-container">
                    <span class="phone-block-label"><i class="fa-solid fa-headset"></i> Reach Us On</span>
                    <div class="phone-pills">
                        <a href="tel:09078918123" class="phone-pill-btn">
                            <i class="fa-solid fa-phone"></i> 09078918123
                        </a>
                        <a href="tel:09062867658" class="phone-pill-btn">
                            <i class="fa-solid fa-phone"></i> 09062867658
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom-line">
                <p class="footer-copyright">&copy; 2026 <strong>Samprime Autos Global</strong>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    <script src="assets/js/main.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/main.js'); ?>"></script>
</body>
</html>
