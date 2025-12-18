<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Moterway City</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .carousel-slide {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transition: opacity 0.6s ease-in-out;
        }

        .carousel-slide-1 {
            background-image: url('/images/mosque.jpg');
        }

        .carousel-slide-2 {
            background-image: url('/images/mosque2.jpg');
        }

        .carousel-slide-3 {
            background-image: url('/images/office.jpg');
            /* transform: rotate(270deg); */
        }

        .gradient-overlay {
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.6));
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #features,
        #aboutUs {
            scroll-margin-top: 80px;
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="antialiased scroll-smooth">
    <!-- Contact Ribbon -->
    <div class="bg-slate-900 text-white py-3 px-4 fixed top-0 w-full z-50">
        <div class="max-w-7xl mx-auto flex justify-end items-center gap-6 text-sm">
            <a href="tel:0321-6382267" class="hover:text-slate-300 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                0321-6382267
            </a>
            <a href="mailto:info@moterway.com" class="hover:text-slate-300 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                info@moterway.com
            </a>
            <a href="https://facebook.com" target="_blank"
                class="hover:text-slate-300 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                </svg>
                Facebook
            </a>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="bg-white backdrop-blur-sm shadow-sm fixed top-[2.8rem] w-full z-40 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 lg:py-4">
            <div class="flex justify-between items-center h-16">
                <img src="/images/motorway_logo.png" alt="Brand Logo" class="h-20 w-20 object-cover rounded-lg">

                <ul class="hidden md:flex items-center space-x-8">
                    <li>
                        <a href="#home" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="#features" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">
                            Features
                        </a>
                    </li>
                    <li>
                        <a href="#aboutUs" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">
                            About
                        </a>
                    </li>
                    <li>
                        <a href="#contact" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">
                            Contact
                        </a>
                    </li>
                </ul>

                <button class="md:hidden text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Fullscreen Carousel -->
    <div id="home" class="relative h-screen mt-[8.8rem] overflow-hidden">
        <div class="carousel-track flex h-full transition-transform duration-700 ease-in-out">
            <div class="carousel-slide carousel-slide-1 min-w-full h-full relative flex items-center justify-center">
                {{-- <div class="absolute inset-0 gradient-overlay"></div>
                <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto animate-fade-in-up">
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight"
                        style="text-shadow: 0 4px 12px rgba(0,0,0,0.4);">
                        Transform Your Business
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-slate-100 font-light"
                        style="text-shadow: 0 2px 8px rgba(0,0,0,0.4);">
                        Innovative solutions that drive real results
                    </p>
                    <a href="#contact"
                        class="inline-block bg-white text-slate-900 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-slate-100 transition-all transform hover:-translate-y-1 hover:shadow-2xl">
                        Get Started
                    </a>
                </div> --}}
            </div>
            <div class="carousel-slide carousel-slide-2 min-w-full h-full relative flex items-center justify-center">
                {{-- <div class="absolute inset-0 gradient-overlay"></div>
                <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight"
                        style="text-shadow: 0 4px 12px rgba(0,0,0,0.4);">
                        Collaborate & Innovate
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-slate-100 font-light"
                        style="text-shadow: 0 2px 8px rgba(0,0,0,0.4);">
                        Building the future together
                    </p>
                    <a href="#features"
                        class="inline-block bg-white text-slate-900 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-slate-100 transition-all transform hover:-translate-y-1 hover:shadow-2xl">
                        Learn More
                    </a>
                </div> --}}
            </div>
            <div class="carousel-slide carousel-slide-3 min-w-full h-full relative flex items-center justify-center">
                {{-- <div class="absolute inset-0 gradient-overlay"></div>
                <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight"
                        style="text-shadow: 0 4px 12px rgba(0,0,0,0.4);">
                        Achieve Excellence
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-slate-100 font-light"
                        style="text-shadow: 0 2px 8px rgba(0,0,0,0.4);">
                        Quality and precision in every detail
                    </p>
                    <a href="#contact"
                        class="inline-block bg-white text-slate-900 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-slate-100 transition-all transform hover:-translate-y-1 hover:shadow-2xl">
                        Join Us
                    </a>
                </div> --}}
            </div>
        </div>
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3 z-20">
            <button class="indicator w-12 h-1 bg-white rounded-full transition-all duration-300"></button>
            <button class="indicator w-12 h-1 bg-white/40 rounded-full transition-all duration-300"></button>
            <button class="indicator w-12 h-1 bg-white/40 rounded-full transition-all duration-300"></button>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">Our Features</h2>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">Modern amenities for a comfortable lifestyle</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="/images/mosque.jpg" alt="Mosque" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Mosque (Masjid)</h3>
                        <p class="text-slate-600 leading-relaxed">Beautiful mosque within the community for daily
                            prayers.</p>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="/images/carpet-road.jpeg" alt="Carpet Road" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Carpet Road</h3>
                        <p class="text-slate-600 leading-relaxed">Smooth, well-paved roads ensuring comfortable travel
                            within the community.</p>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?w=400&q=80"
                        alt="Green Belt Road" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Green Belt Road</h3>
                        <p class="text-slate-600 leading-relaxed">Wide, tree-lined roads providing a scenic and
                            eco-friendly environment.</p>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1508615039623-a25605d2b022?w=400&q=80"
                        alt="Street Lights" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Street Lights</h3>
                        <p class="text-slate-600 leading-relaxed">Well-lit streets throughout the community for safety
                            and security.</p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1587502537147-2ba64a117a23?w=400&q=80"
                        alt="Community Park" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Community Park</h3>
                        <p class="text-slate-600 leading-relaxed">Beautiful green spaces for families to relax and
                            children to play.</p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1557597774-9d273605dfa9?w=400&q=80"
                        alt="24 Hour Security" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">24 Hour Security</h3>
                        <p class="text-slate-600 leading-relaxed">Round-the-clock security personnel ensuring your
                            peace of mind.</p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1557324232-b8917d3c3dcb?w=400&q=80" alt="CCTV Cameras"
                        class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">CCTV Cameras</h3>
                        <p class="text-slate-600 leading-relaxed">Advanced surveillance system monitoring all areas
                            24/7.</p>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1513467535987-fd81bc7d62f8?w=400&q=80"
                        alt="Wall Boundary" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Wall Boundary</h3>
                        <p class="text-slate-600 leading-relaxed">Secure boundary wall surrounding the entire
                            community.</p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=400&q=80"
                        alt="Colony Gate" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Colony Gate</h3>
                        <p class="text-slate-600 leading-relaxed">Controlled access with modern gate system and
                            security checks.</p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=400&q=80"
                        alt="Sewage System" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Sewage System</h3>
                        <p class="text-slate-600 leading-relaxed">Modern underground sewage system for proper waste
                            management.</p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1548839140-29a749e1cf4d?w=400&q=80"
                        alt="Water Filtration" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Water Filtration Plant</h3>
                        <p class="text-slate-600 leading-relaxed">Clean, filtered water supply ensuring health and
                            hygiene.</p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1555992336-fb0d29498b13?w=400&q=80"
                        alt="Commercial Market" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-900 mb-3">Commercial Market & School</h3>
                        <p class="text-slate-600 leading-relaxed">Convenient shopping and quality education within the
                            community.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="aboutUs" class="py-24 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">About Us</h2>
                <div class="w-20 h-1 bg-blue-600 mx-auto mb-8"></div>
            </div>
            <div class="space-y-6 text-lg text-slate-600 leading-relaxed">
                <p>
                    We are a team of passionate innovators dedicated to creating exceptional digital experiences. With
                    years of expertise and an unwavering commitment to excellence, we help businesses transform their
                    vision into reality.
                </p>
                <p>
                    Our approach combines cutting-edge technology with creative thinking to deliver solutions that not
                    only meet but exceed expectations. We believe in building lasting partnerships and being a catalyst
                    for our clients' success.
                </p>
                <p>
                    Join us on this journey and discover what makes us stand out in the industry.
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 bg-slate-50">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">Get In Touch</h2>
                <p class="text-xl text-slate-600">We'd love to hear from you</p>
            </div>
            <form class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Name</label>
                    <input type="text" placeholder="Your name"
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                    <input type="email" placeholder="your@email.com"
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                    <textarea placeholder="How can we help?" rows="5"
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none resize-none"></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-slate-900 text-white py-4 rounded-lg font-semibold text-lg hover:bg-slate-800 transition-all transform hover:-translate-y-1 hover:shadow-lg">
                    Send Message
                </button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-slate-400">&copy; 2024 Brand. All rights reserved.</p>
        </div>
    </footer>

    <script>
        const track = document.querySelector('.carousel-track');
        const indicators = document.querySelectorAll('.indicator');
        const carouselContainer = document.querySelector('#home');
        let current = 0;
        const total = 3;
        let autoPlayInterval;
        let isHovering = false;
        let startX = 0;
        let currentX = 0;
        let isDragging = false;

        function updateSlide(index) {
            current = index % total;
            if (current < 0) current = total - 1;
            track.style.transform = `translateX(-${current * 100}%)`;
            indicators.forEach((ind, i) => {
                if (i === current) {
                    ind.classList.remove('bg-white/40');
                    ind.classList.add('bg-white');
                } else {
                    ind.classList.remove('bg-white');
                    ind.classList.add('bg-white/40');
                }
            });
        }

        function startAutoPlay() {
            autoPlayInterval = setInterval(() => {
                if (!isHovering && !isDragging) {
                    updateSlide(current + 1);
                }
            }, 2000);
        }

        function stopAutoPlay() {
            clearInterval(autoPlayInterval);
        }

        // Pause on hover
        carouselContainer.addEventListener('mouseenter', () => {
            isHovering = true;
        });

        carouselContainer.addEventListener('mouseleave', () => {
            isHovering = false;
        });

        // Touch/Swipe handling
        carouselContainer.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
        });

        carouselContainer.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            currentX = e.touches[0].clientX;
        });

        carouselContainer.addEventListener('touchend', () => {
            if (!isDragging) return;
            const diff = startX - currentX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    updateSlide(current + 1);
                } else {
                    updateSlide(current - 1);
                }
            }
            isDragging = false;
        });

        // Mouse drag handling
        carouselContainer.addEventListener('mousedown', (e) => {
            startX = e.clientX;
            isDragging = true;
            carouselContainer.style.cursor = 'grabbing';
        });

        carouselContainer.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            currentX = e.clientX;
        });

        carouselContainer.addEventListener('mouseup', () => {
            if (!isDragging) return;
            const diff = startX - currentX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    updateSlide(current + 1);
                } else {
                    updateSlide(current - 1);
                }
            }
            isDragging = false;
            carouselContainer.style.cursor = 'grab';
        });

        carouselContainer.addEventListener('mouseleave', () => {
            if (isDragging) {
                isDragging = false;
                carouselContainer.style.cursor = 'grab';
            }
        });

        carouselContainer.style.cursor = 'grab';

        indicators.forEach((ind, i) => {
            ind.addEventListener('click', () => updateSlide(i));
        });

        // Start auto-play
        startAutoPlay();

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>

</html>
