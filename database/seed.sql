-- RimonTech Seed Data
USE rimontech;

-- Admins (admin@rimontech.com / Admin@123)
INSERT INTO admins (name, email, password, role) VALUES
('Rimon', 'admin@rimontech.com', '$2y$10$IJf7bo8GAYTfPKNeLnZJ5uJ71f6hTJcR8IAG7riTRJ8GeSkBeife.', 'admin');

-- Customers (customer@rimontech.com / Customer@123)
INSERT INTO customers (name, email, phone, company, address, password, status) VALUES
('Karim Hossain', 'customer@rimontech.com', '+880 1711-223344', 'Karim Traders', 'Mirpur-10, Dhaka', '$2y$10$Kegfd9izVasrW1/NsQ9ww.WXMwLb79Bz63YYszYddhh1WQnWpiIW.', 'active'),
('Sadia Rahman', 'sadia@demo.com', '+880 1812-334455', 'Sadia Fashion', 'Uttara, Dhaka', '$2y$10$Kegfd9izVasrW1/NsQ9ww.WXMwLb79Bz63YYszYddhh1WQnWpiIW.', 'active'),
('Tanvir Ahmed', 'tanvir@demo.com', '+880 1913-445566', 'Tanvir Engineering', 'Dhanmondi, Dhaka', '$2y$10$Kegfd9izVasrW1/NsQ9ww.WXMwLb79Bz63YYszYddhh1WQnWpiIW.', 'active');

-- Services
INSERT INTO services (title, slug, short_desc, icon, description, features, active) VALUES
('Website Development', 'website-development', 'Modern, fast and responsive business websites built with the latest technology.', 'code', 'We design and develop modern, high-performance websites that put your business on the digital map. From clean corporate sites to complex platforms, every build is fast, secure and mobile-first.', JSON_ARRAY('Responsive & mobile-first design','SEO-friendly code','Fast loading performance','CMS integration','SSL & security setup','Free 30-day support'), 1),
('E-Commerce', 'ecommerce', 'Complete online stores with cart, payment gateway and order management.', 'cart', 'Launch your own online store with a seamless shopping experience. We integrate secure payment gateways, inventory management and analytics so you can sell anywhere, anytime.', JSON_ARRAY('Product catalog management','Secure payment gateway','Order & inventory tracking','Shipping integration','Customer accounts','Sales analytics'), 1),
('Custom Web Application', 'web-application', 'Tailor-made web apps for your unique business processes.', 'app', 'Your business is unique, so is your software. We build custom web applications that automate workflows, manage data and scale with your business growth.', JSON_ARRAY('Requirement analysis','Custom database design','Role-based access control','API integration','Scalable architecture','Ongoing maintenance'), 1),
('Landing Page', 'landing-page', 'High-converting landing pages for campaigns, launches and leads.', 'rocket', 'Capture attention and convert visitors with focused, fast landing pages designed around a single goal. Perfect for product launches, ad campaigns and lead generation.', JSON_ARRAY('Conversion-focused design','A/B testing ready','Lead capture forms','Analytics tracking','Mobile optimized','Fast launch in 48 hours'), 1),
('SEO & Optimization', 'seo', 'Get found on Google and grow organic traffic with proven SEO strategies.', 'search', 'Our SEO service helps your website rank higher on Google, attract the right audience and turn search traffic into customers with technical and on-page optimization.', JSON_ARRAY('Technical SEO audit','Keyword research','On-page optimization','Page speed optimization','Local SEO setup','Monthly performance reports'), 1),
('Website Maintenance', 'maintenance', 'Keep your website secure, updated and running smoothly 24/7.', 'shield', 'A website needs care to stay fast and secure. Our maintenance plans include updates, backups, security monitoring and content changes so you can focus on business.', JSON_ARRAY('Regular backups','Security monitoring','Plugin & core updates','Performance checks','Content updates','Priority support'), 1);

-- Solutions
INSERT INTO solutions (title, slug, category, short_desc, icon, description, features, active) VALUES
('Business Website', 'business-website', 'Business', 'Professional corporate website that builds trust and drives enquiries.', 'briefcase', 'A professional business website that represents your brand, showcases your offerings and turns visitors into enquiries.', JSON_ARRAY('Corporate design','About & team pages','Services showcase','Contact & inquiry forms','Google Maps integration'), 1),
('E-Commerce Store', 'ecommerce-store', 'E-Commerce', 'Ready-to-launch online store for retail and wholesale businesses.', 'cart', 'Full-featured online stores with product management, secure checkout and delivery integration for retail success.', JSON_ARRAY('Unlimited products','Secure checkout','Order management','Coupons & discounts','Payment gateway'), 1),
('School Website', 'school-website', 'Education', 'Complete website for schools, colleges and coaching centers.', 'graduation', 'Digital presence for educational institutions with admissions, notices, results and gallery management.', JSON_ARRAY('Admission forms','Notice board','Result publishing','Faculty profiles','Photo gallery'), 1),
('Restaurant Website', 'restaurant-website', 'Food', 'Appetizing website with online menu and table reservations.', 'utensils', 'Showcase your menu, attract food lovers and let customers reserve tables or order online with ease.', JSON_ARRAY('Digital menu','Table reservation','Online ordering','Photo gallery','Location & hours'), 1),
('Clinic Website', 'clinic-website', 'Healthcare', 'Trust-building website for clinics, doctors and hospitals.', 'stethoscope', 'Healthcare websites that give patients confidence with doctor profiles, appointment booking and service info.', JSON_ARRAY('Doctor profiles','Appointment booking','Service listing','Patient testimonials','Emergency info'), 1),
('Hotel Website', 'hotel-website', 'Hospitality', 'Bookings, rooms and amenities showcased beautifully.', 'bed', 'Elegant hotel websites with room showcases, direct booking and facilities that convert visitors into guests.', JSON_ARRAY('Room showcase','Direct booking','Amenities & gallery','Offers & packages','Location & travel info'), 1),
('Portfolio Website', 'portfolio-website', 'Personal', 'Stunning personal portfolio to showcase your work and skills.', 'user', 'A personal brand website that presents your work beautifully and helps you win clients and opportunities.', JSON_ARRAY('Project showcase','About & skills','Blog ready','Contact form','Social media links'), 1),
('Custom Software', 'custom-software', 'Software', 'Business software built exactly around your workflow.', 'cpu', 'From inventory systems to automation tools, we build software that removes manual work and grows with you.', JSON_ARRAY('Custom workflows','Data management','Reporting & dashboards','Third-party API integration','Training & support'), 1);

-- Portfolio items
INSERT INTO portfolio_items (title, slug, category, image, tech_stack, description, features, live_demo_url, github_url, case_study, featured, active) VALUES
('Job Tracking App', 'jobtracking-app', 'Web Application', 'uploads/portfolio/jobtracking.png', 'Next.js, Node.js, MongoDB', 'A job application tracker built with a Node.js backend and Next.js frontend — manage applications, track progress by status, view monthly trends and monitor employer interest.', JSON_ARRAY('Secure dashboard login','Add, update & delete applications','Filter by status','Search by company, role, location','Monthly application trends','Employer interest tracking'), 'https://jobtracking-rust.vercel.app', 'https://github.com/rimon213311004/Jobtracking', NULL, 1, 1),
('ATS CV Maker', 'ats-cv-maker', 'Web Application', 'uploads/portfolio/ats-cv-maker.png', 'React, Laravel, MySQL', 'An AI-assisted resume builder that generates ATS-friendly CVs and scores them against job descriptions.', JSON_ARRAY('ATS score checker','ATS-friendly templates','AI content suggestions','PDF export','Version history'), 'https://demo.rimontech.com/ats-cv', NULL, 'Over 5,000 resumes built in the first three months. Users improved their ATS match scores by an average of 34%.', 1, 1),
('AR Traders Rice Shop', 'ar-traders-rice-shop', 'E-Commerce', 'uploads/portfolio/ar-traders.png', 'Next.js 16, TypeScript, Tailwind CSS, MongoDB, Recharts, jsPDF', 'Rice shop management system for inventory, sales, customers and dues — an owner dashboard plus phone-based customer lookup without login.', JSON_ARRAY('Owner dashboard (sales, cash, dues)','Customer lookup by phone number','Inventory & supplier management','Analytics with Recharts','PDF receipts with jsPDF'), 'https://rice-shop-rho.vercel.app', 'https://github.com/rimon213311004/Shop', NULL, 1, 1),
('Karim Traders Website', 'karim-traders-website', 'Business Website', 'uploads/portfolio/karim-traders.png', 'HTML, CSS, PHP', 'Corporate website for a trading company with service showcase and enquiry management.', JSON_ARRAY('Service showcase','Product catalog','Enquiry forms','WhatsApp integration'), 'https://demo.rimontech.com/karim-traders', NULL, 'Enquiries tripled within two months of launch, with most leads coming through the new contact forms.', 1, 1),
('Sadia Fashion Store', 'sadia-fashion-store', 'E-Commerce', 'uploads/portfolio/sadia-fashion.png', 'Vue.js, Laravel, MySQL', 'Online fashion store with bKash and Nagad payment integration and an easy-to-manage admin panel.', JSON_ARRAY('bKash & Nagad payments','Order management','Size & color variants','Coupon system','Sales dashboard'), 'https://demo.rimontech.com/sadia-fashion', NULL, 'Monthly online orders grew from 20 to 150 within the first quarter.', 1, 1),
('Uttara Clinic Portal', 'uttara-clinic-portal', 'Healthcare', 'uploads/portfolio/uttara-clinic.png', 'React, Node.js, PostgreSQL', 'Clinic website with online appointment booking and doctor management for patients and staff.', JSON_ARRAY('Online appointments','Doctor schedules','Patient records portal','Automated reminders','Mobile responsive'), 'https://demo.rimontech.com/uttara-clinic', NULL, 'Appointment no-shows reduced by 40% thanks to automated reminders.', 0, 1);

INSERT INTO portfolio_items
(title, slug, category, image, tech_stack, description, features, live_demo_url, github_url, featured, active)
VALUES
('TicketBus', 'ticketbus', 'Web Application', 'uploads/portfolio/ticketbus.png', 'React 19, Node.js, Express, MongoDB', 'A modern bus booking platform for Bangladesh with exact-seat selection, verified payments and live operations control.', JSON_ARRAY('Exact seat selection','Verified payments','Live operations control','Real-time seat map','Operations dashboard'), 'https://ticket-bus-client.vercel.app', 'https://github.com/rimon213311004/TicketBus', 1, 1),
('Raihan Rimon Portfolio', 'raihan-rimon-portfolio', 'Portfolio', 'uploads/portfolio/raihan-portfolio.png', 'Next.js, Three.js, Framer Motion', 'A modern, 3D, fully responsive personal portfolio built with Next.js, Three.js and Framer Motion — interactive 3D hero with glassmorphism UI.', JSON_ARRAY('Interactive 3D hero (react-three-fiber)','3D tilt cards & scroll reveal','Glassmorphism UI','Fully responsive','Framer Motion animations'), 'https://raihan-rimon-portfolio.vercel.app', 'https://github.com/rimon213311004/Potolio', 1, 1),
('Hostel Meal Management', 'hostel-meal-management', 'Web Application', 'uploads/portfolio/meal-hoster.png', 'Next.js 14, Node.js, MongoDB (MERN)', 'A full-stack MERN application to manage a hostel mess — deposits, meals, shopping costs and automatic monthly settlements with a premium 3D-animated UI.', JSON_ARRAY('Member deposits','Meal tracking','Shopping cost tracking','Automatic monthly settlement','3D-animated UI'), 'https://meal-ffdz.vercel.app', 'https://github.com/rimon213311004/Meal', 0, 1),
('School Management System', 'school-management-system', 'School', 'uploads/portfolio/school-management.png', 'PHP, MySQL', 'A full-featured school management web application with separate admin and teacher access — manage teachers, students, classes, routines, leave requests and basic reports.', JSON_ARRAY('Admin & teacher roles','Student & teacher records','Classes & sections','Class routines','Leave requests','School reports & statistics'), NULL, 'https://github.com/rimon213311004/School_Management', 0, 1),
('Gas Buddy BD', 'gas-buddy-bd', 'Business Website', 'uploads/portfolio/gas-buddy-bd.png', 'PHP, Tailwind CSS', 'Official website for Gas Buddy BD — a Bangladeshi provider of complete LPG, CNG and SNG gas systems — public marketing site, enquiry forms, client accounts and a full admin dashboard.', JSON_ARRAY('Public marketing site','Online enquiry forms','Client accounts','Corporate brochure download','Admin dashboard'), NULL, 'https://github.com/rimon213311004/Gasbuddu_Update', 0, 1),
('GasBuddy Platform', 'gasbuddy-platform', 'Web Application', 'uploads/portfolio/gasbuddy-app.png', 'TypeScript', 'GasBuddy-style fuel and gas services web platform built with TypeScript.', JSON_ARRAY('Fuel price comparison','Service listing','Modern responsive UI'), NULL, 'https://github.com/rimon213311004/GassBudy', 0, 1),
('GasBuddy v2', 'gasbuddy-v2', 'Web Application', 'uploads/portfolio/gasbuddy-v2.png', 'Web, API', 'Second iteration of the GasBuddy fuel services platform — refined UI and improved data flow.', JSON_ARRAY('Refined UI','Improved data handling','Service comparison'), NULL, 'https://github.com/rimon213311004/Gasbuddy2', 0, 1),
('WhatsApp AI Bot', 'whatsapp-ai-bot', 'Web Application', 'uploads/portfolio/whatsapp-ai-bot.png', 'JavaScript, Claude AI', 'A WhatsApp bot powered by Claude AI that responds with intelligent, context-aware replies — maintains conversation history and supports fallback models for reliability.', JSON_ARRAY('AI-powered responses','Conversation history','Multi-language replies','Fallback models','Persistent chat context'), NULL, 'https://github.com/rimon213311004/Whatsapp', 0, 1),
('BDJobs Auto-Apply', 'bdjobs-auto-apply', 'Web Application', 'uploads/portfolio/bdjobs-automation.png', 'Python, Playwright', 'Automates the BDJobs job application workflow — persistent browser session, job search with CSV export, AI-generated cover letters and batch auto-application.', JSON_ARRAY('Persistent login session','Job search & CSV export','AI-generated cover letters','Batch auto-apply','Modular step scripts'), NULL, 'https://github.com/rimon213311004/Automation', 0, 1),
('Portfolio Design v2', 'portfolio-design-v2', 'Portfolio', 'uploads/portfolio/portfolio-design-v2.png', 'TypeScript', 'A modern portfolio design built with TypeScript — clean layouts and smooth interactions.', JSON_ARRAY('Modern layout','Responsive design','Smooth interactions'), 'https://portfoliodesign2.vercel.app', 'https://github.com/rimon213311004/portfoliodesign2', 0, 1),
('Portfolio Design v1', 'portfolio-design-v1', 'Portfolio', 'uploads/portfolio/portfolio-design-v1.png', 'Next.js, TypeScript', 'A responsive Next.js portfolio template design — a clean starting point for personal sites.', JSON_ARRAY('Next.js + TypeScript','Responsive layouts','Clean components'), 'https://portfolio-design1-five.vercel.app', 'https://github.com/rimon213311004/portfolio_design1', 0, 1);

-- Downloads
INSERT INTO downloads (project_id, file_name, file_path, download_enabled, download_count) VALUES
(1, 'job-tracking-app.zip', 'downloads/projects/job-tracking-app.zip', 1, 127),
(2, 'ats-cv-maker.zip', 'downloads/projects/ats-cv-maker.zip', 1, 89),
(3, 'rice-shop-inventory.zip', 'downloads/projects/rice-shop-inventory.zip', 1, 64),
(4, 'karim-traders-website.zip', 'downloads/projects/karim-traders-website.zip', 0, 21);

-- Pricing plans
INSERT INTO pricing_plans (title, price, period, description, features, highlighted, active) VALUES
('Starter', 15000, 'one-time', 'Perfect for small businesses getting online.', JSON_ARRAY('Up to 5 pages','Responsive design','Contact form','Mobile optimized','Basic SEO setup'), 0, 1),
('Business', 35000, 'one-time', 'For growing businesses that need more power.', JSON_ARRAY('Up to 15 pages','E-commerce ready','CMS integration','Advanced SEO','Payment gateway','3 months support'), 1, 1),
('Custom', 80000, 'one-time', 'Tailor-made applications for unique needs.', JSON_ARRAY('Custom features','Database design','Admin panel','API integration','Priority support','Ongoing maintenance'), 0, 1),
('Maintenance', 3000, 'monthly', 'Keep your website secure and updated.', JSON_ARRAY('Monthly backups','Security monitoring','Updates & patches','Content updates','Performance checks','Priority support'), 0, 1);

-- Testimonials
INSERT INTO testimonials (customer_name, role, company, content, rating, active) VALUES
('Karim Hossain', 'Owner', 'Karim Traders', 'RimonTech delivered our company website on time and the design is outstanding. Enquiries have doubled since launch!', 5, 1),
('Sadia Rahman', 'Founder', 'Sadia Fashion', 'My online store was live in just two weeks. The bKash integration works perfectly and sales are growing every month.', 5, 1),
('Tanvir Ahmed', 'Director', 'Tanvir Engineering', 'The custom inventory system saved us hours every day. Professional team, clear communication and great support.', 5, 1),
('Nusrat Jahan', 'Principal', 'Ideal School', 'Our school website looks amazing and parents love the notice board and result system. Highly recommended.', 4, 1);

-- Projects
INSERT INTO projects (customer_id, title, description, status, progress, start_date, due_date) VALUES
(1, 'Karim Traders Website', 'Corporate website with product catalog and enquiry forms.', 'delivered', 100, '2026-01-10', '2026-02-15'),
(2, 'Sadia Fashion Store', 'Online fashion store with bKash and Nagad payment integration.', 'in_progress', 65, '2026-05-01', '2026-07-30'),
(3, 'Inventory Management System', 'Custom inventory and billing system for engineering spare parts.', 'review', 90, '2026-03-01', '2026-05-20'),
(1, 'Karim Traders E-Commerce', 'Upgrade company website into a full online store.', 'planning', 10, '2026-07-15', '2026-10-15');

-- Project requests
INSERT INTO project_requests (customer_id, name, email, phone, service_type, budget, message, status) VALUES
(1, 'Karim Hossain', 'customer@rimontech.com', '+880 1711-223344', 'E-Commerce', 'BDT 35,000 - 50,000', 'I want to upgrade my company website into a full online store with bKash payment.', 'in_progress'),
(2, 'Sadia Rahman', 'sadia@demo.com', '+880 1812-334455', 'Custom Web Application', 'BDT 80,000+', 'Need a customer loyalty system for my fashion business.', 'new'),
(NULL, 'Rafiq Islam', 'rafiq@demo.com', '+880 1714-556677', 'Landing Page', 'BDT 10,000 - 15,000', 'Need a landing page for my upcoming product launch.', 'contacted'),
(NULL, 'Farhana Akter', 'farhana@demo.com', '+880 1615-667788', 'Website Development', 'BDT 15,000 - 35,000', 'I run a small bakery and need a simple website with menu and location.', 'new');

-- Messages (contact/public + internal)
INSERT INTO messages (sender_type, sender_id, receiver_type, receiver_id, subject, message, is_read, created_at) VALUES
('public', NULL, NULL, NULL, 'New project enquiry', 'Hi, I am looking for a website for my restaurant. Can you share your packages?', 1, '2026-07-20 10:00:00'),
('public', NULL, NULL, NULL, 'Price question', 'How much does a portfolio website cost with 5 pages?', 1, '2026-07-22 14:30:00'),
('customer', 1, 'admin', NULL, 'About my invoice', 'Hi, when will invoice #INV-2026-001 be paid?', 1, '2026-07-25 09:15:00'),
('admin', NULL, 'customer', 1, 'Invoice updated', 'Your invoice #INV-2026-001 has been marked as paid.', 0, '2026-07-26 11:00:00'),
('customer', 2, 'admin', NULL, 'Design feedback', 'The new homepage design looks great. Can we adjust the color a bit?', 0, '2026-08-01 16:45:00');

-- Invoices
INSERT INTO invoices (customer_id, project_id, invoice_no, amount, status, issue_date, due_date) VALUES
(1, 1, 'INV-2026-001', 35000.00, 'paid', '2026-01-12', '2026-02-01'),
(2, 2, 'INV-2026-002', 30000.00, 'paid', '2026-05-05', '2026-05-25'),
(3, 3, 'INV-2026-003', 80000.00, 'unpaid', '2026-03-10', '2026-04-10'),
(1, 4, 'INV-2026-004', 20000.00, 'unpaid', '2026-07-20', '2026-08-10');

-- Payments
INSERT INTO payments (customer_id, invoice_id, amount, method, trx_id, status, paid_at) VALUES
(1, 1, 35000.00, 'Bank Transfer', 'TX-1001', 'completed', '2026-01-25 12:00:00'),
(2, 2, 15000.00, 'bKash', 'TX-1002', 'completed', '2026-05-15 18:30:00'),
(2, 2, 15000.00, 'bKash', 'TX-1003', 'completed', '2026-05-28 19:00:00'),
(3, 3, 40000.00, 'Nagad', 'TX-1004', 'pending', NULL);

-- Documents
INSERT INTO documents (customer_id, project_id, file_name, file_path, uploaded_by) VALUES
(1, 1, 'project-brief.pdf', 'uploads/documents/project-brief.pdf', 'admin'),
(1, 1, 'final-invoice.pdf', 'uploads/documents/final-invoice.pdf', 'admin'),
(2, 2, 'logo-files.zip', 'uploads/documents/logo-files.zip', 'customer'),
(3, 3, 'requirements.docx', 'uploads/documents/requirements.docx', 'customer');

-- Notifications
INSERT INTO notifications (user_type, user_id, title, message, link, is_read, created_at) VALUES
('admin', 1, 'New project request', 'Sadia Rahman submitted a new project request.', 'admin/project-requests.php', 1, '2026-08-01 10:00:00'),
('admin', 1, 'New message', 'Rafiq Islam sent you a message.', 'admin/messages.php', 0, '2026-08-02 11:00:00'),
('customer', 1, 'Invoice paid', 'Your invoice #INV-2026-001 has been marked as paid.', 'customer/invoices.php', 1, '2026-07-26 11:30:00'),
('customer', 1, 'Project delivered', 'Your project "Karim Traders Website" has been delivered.', 'customer/projects.php', 0, '2026-07-30 09:00:00'),
('customer', 2, 'New reply from admin', 'The admin replied to your support ticket.', 'customer/support.php', 0, '2026-08-01 17:00:00');

-- Support tickets
INSERT INTO support_tickets (customer_id, subject, message, status, created_at) VALUES
(1, 'How to update website content?', 'Can I update the product list on my website myself?', 'open', '2026-07-28 10:00:00'),
(2, 'Payment gateway issue', 'A customer could not pay using bKash last night.', 'answered', '2026-08-01 15:00:00');

INSERT INTO support_replies (ticket_id, sender_type, message, created_at) VALUES
(2, 'admin', 'The issue was resolved. It was a temporary bKash downtime on their end.', '2026-08-01 17:00:00');

-- Settings
INSERT INTO settings (setting_key, setting_value) VALUES
('contact_email', 'hello@rimontech.com'),
('contact_phone', '+880 1712-345678'),
('contact_address', 'Level 4, House 21, Road 7, Dhanmondi, Dhaka'),
('facebook_url', 'https://facebook.com/rimontech'),
('linkedin_url', 'https://linkedin.com/company/rimontech'),
('twitter_url', 'https://twitter.com/rimontech'),
('github_url', 'https://github.com/rimon213311004'),
('google_maps', 'https://maps.google.com/?q=Dhanmondi,Dhaka'),
('about_text', 'RimonTech is a web development agency based in Dhaka, Bangladesh. We help businesses launch fast, secure and beautiful websites and web applications. From a simple landing page to a complete e-commerce platform, we build digital products that grow with your business.'),
('mission_text', 'To make professional web development accessible for businesses of every size with honest pricing and reliable delivery.'),
('vision_text', 'To become the most trusted web development partner for small and growing businesses across South Asia.'),
('homepage_tagline', 'We build fast, beautiful and reliable websites that grow your business.');
