-- Library Management System
-- Run this script in MySQL to create the project database and sample data

CREATE DATABASE IF NOT EXISTS `library_senegal` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `library_senegal`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','staff') NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL UNIQUE,
  `description` VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `students` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_number` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `program` VARCHAR(120) NOT NULL,
  `academic_year` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `books` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `isbn` VARCHAR(20) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(120) NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `year` YEAR NOT NULL,
  `copies_total` INT UNSIGNED NOT NULL DEFAULT 5,
  `copies_available` INT UNSIGNED NOT NULL DEFAULT 5,
  `summary` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `borrowings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT UNSIGNED NOT NULL,
  `book_id` INT UNSIGNED NOT NULL,
  `borrow_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `returned` TINYINT(1) NOT NULL DEFAULT 0,
  `returned_date` DATE DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
('Administrator', 'admin@senlibrary.edu', '$2y$10$hQ/9Ac2G3z55MctyL1F03OZk3e7bGr1jiCoxRzzeE2D1gZJ4mo7Me', 'admin');

INSERT IGNORE INTO `categories` (`name`, `description`) VALUES
('Literature', 'Novels, poetry, and literary works from around the world.'),
('History', 'Historical books covering world civilizations and modern history.'),
('Technology', 'Computer science, programming, and digital innovation resources.'),
('Business', 'Entrepreneurship, economics, and business development.');

INSERT IGNORE INTO `students` (`student_number`, `name`, `email`, `program`, `academic_year`) VALUES
('STU-2101','Amina Diouf','amina.diouf@senlibrary.edu','Littérature','Licence 2'),
('STU-2102','Cheikh Fall','cheikh.fall@senlibrary.edu','Histoire','Licence 3'),
('STU-2103','Fatou Ndiaye','fatou.ndiaye@senlibrary.edu','Technologie','Master 1'),
('STU-2104','Mamadou Ba','mamadou.ba@senlibrary.edu','Affaires','Licence 2'),
('STU-2105','Ndeye Sarr','ndeye.sarr@senlibrary.edu','Littérature','Licence 3'),
('STU-2106','Ousmane Cissé','ousmane.cisse@senlibrary.edu','Technologie','Licence 1'),
('STU-2107','Seynabou Faye','seynabou.faye@senlibrary.edu','Histoire','Master 2'),
('STU-2108','Ibrahima Ndiaye','ibrahima.ndiaye@senlibrary.edu','Technologie','Licence 2'),
('STU-2109','Ndèye Gaye','ndeye.gaye@senlibrary.edu','Affaires','Licence 1'),
('STU-2110','Papa Sène','papa.sene@senlibrary.edu','Littérature','Licence 3');

INSERT IGNORE INTO `books` (`isbn`, `title`, `author`, `category_id`, `year`, `copies_total`, `copies_available`, `summary`) VALUES
('978-0143015847','So Long a Letter','Mariama Bâ',1,1988,7,5,'An intimate reflection on love, loss, and social reform in Senegal.'),
('978-0143039776','God&apos;s Bits of Wood','Ousmane Sembène',1,1962,6,4,'A moving portrait of a strike and collective protest in colonial West Africa.'),
('978-0143108991','The Labyrinth of Solitude','Cheikh Hamidou Kane',1,1980,6,3,'Philosophical novel exploring faith and identity during colonial years.'),
('978-0679737537','Tales of Amadou Koumba','Birago Diop',1,1957,5,5,'A book of magical African folktales and lyrical storytelling.'),
('978-0804840037','Senegal: An African Nation Between Islam and the West','Richard L. Roberts',2,2003,6,6,'A modern history of Senegal and its path through independence.'),
('978-0521634318','Africa: A Biography of the Continent','John Reader',2,1998,4,4,'A sweeping history of Africa from ancient kingdoms to modern times.'),
('978-0814339010','The History of West Africa','J. F. Ade Ajayi',2,2002,5,4,'Classic scholarship on West African trade, empires, and culture.'),
('978-0195307092','African Civilizations: An Archaeological Perspective','G. Mokhtar',2,1990,5,3,'A study of the archaeological foundations of African civilizations.'),
('978-1492057611','Introduction to Programming with Python','John Guttag',3,2020,7,7,'Clear beginner-friendly programming techniques using Python.'),
('978-0134685991','Computer Science: An Interdisciplinary Approach','Robert Sedgewick',3,2016,6,5,'A modern introduction to computer science and algorithmic thinking.'),
('978-0262033848','Artificial Intelligence: A Modern Approach','Stuart Russell',3,2015,5,5,'The standard textbook for AI concepts, search, and machine learning.'),
('978-1491920497','Data Science from Scratch','Joel Grus',3,2019,5,5,'Hands-on introduction to data science using Python and practical examples.'),
('978-1786631117','Entrepreneurship in Africa','Mia Berggren',4,2018,5,5,'Strategies for building businesses across African markets.'),
('978-0198783540','African Economic Development','Claudia D. Williamson',4,2017,6,6,'A survey of economic strategies in modern Africa.'),
('978-1478010211','The Dakar Digital Campus','Amina Diop',3,2021,4,4,'A case study of digital education growth in Senegal.'),
('978-9781291430','Senegalese University Life','Fatou Ndiaye',4,2019,5,5,'Personal stories of students in Senegal&apos;s major universities.'),
('978-1849014099','The African Imagination','Wole Soyinka',1,2001,4,4,'Essays on African literature, memory, and creative expression.'),
('978-0143123628','The Wedding of Zein','Tayeb Salih',1,1967,4,4,'A lyrical novella about community, love, and tradition.'),
('978-0520298190','The Kingdom of Siyu: The Political Structure of a Wolof State','Boubacar Barry',2,2010,4,4,'A historical study of Wolof politics and social structure.'),
('978-0571327434','African Technology Today','James Ogundele',3,2014,4,4,'A survey of technology adoption and innovation in Africa.'),
('978-1982101561','Startup Africa','Kofi Mensah',4,2020,5,5,'Entrepreneurial guidance for young African founders.'),
('978-0321982384','Web Development with PHP & MySQL','Luke Welling',3,2016,4,4,'Hands-on tutorials for building database-backed web applications.'),
('978-0761364668','The Griot&apos;s Tale','Ahmed Sekou',1,2018,4,4,'A contemporary novel that honors West African oral storytelling tradition.'),
('978-0470850409','African Business Leadership','Dambisa Moyo',4,2013,5,4,'Insights on leadership challenges and opportunities in Africa.'),
('978-0852554028','The Making of Modern Senegal','Abdoulaye Dieye',2,2012,4,4,'A cultural and political history of Senegal&apos;s modern era.'),
('978-0812290222','African Folklore and Mythology','Patricia Ann Lynch',1,2011,5,5,'Myths, proverbs, and folktales from across Africa.'),
('978-0415300620','Colonial Senegal and the Civilizing Mission','Emily Gilmore',2,2014,5,5,'Analysis of colonial education and administration in Senegal.'),
('978-1449376274','Mobile Applications for African Students','Nadia Bah',3,2017,4,4,'Practical mobile app development projects with local themes.'),
('978-0933072715','Education and Social Change in Africa','Miriam Were',2,2008,4,4,'How education transforms communities across Africa.'),
('978-1406632107','African Literature Today','Isidore Okpewho',1,1999,4,4,'Essays on postcolonial African narrative forms.'),
('978-1479808115','Learning PHP, MySQL & JavaScript','Robin Nixon',3,2018,5,5,'A beginner-friendly introduction to full-stack web development.'),
('978-1471869418','Digital Libraries in Africa','Nafi Diouf',2,2021,4,4,'The evolution of digital library systems in African universities.'),
('978-1492050047','Modern Data Analytics','Claire Délizy',3,2020,4,4,'Data analytics techniques for business intelligence.'),
('978-1119568126','Economic Policy in Africa','John Page',4,2018,4,4,'Policy frameworks for sustainable African growth.'),
('978-1447315670','The Urban Senegalese Classroom','Seynabou Faye',2,2016,4,4,'A look at student life and education in urban Senegal.'),
('978-0804843502','African Poetry Today','Jennifer Lee',1,2013,4,4,'A collection of modern African poetry and critical essays.'),
('978-1119363410','Cryptography and Network Security','William Stallings',3,2017,4,4,'Core security principles for computing systems.'),
('978-1405881838','Public Administration in West Africa','Alioune Ndiaye',4,2015,4,4,'Governance and administrative reform in West Africa.'),
('978-0719068776','Senegal&apos;s Postcolonial Culture','Kadidiatou Thiam',2,2019,5,5,'Cultural resilience and identity after independence.'),
('978-1474274218','Big Data for African Cities','Seynabou Niang',3,2020,4,4,'How big data is reshaping African urban planning.'),
('978-0253036940','African Entrepreneurial Mindset','Emmanuel Adegoke',4,2017,5,4,'Stories of African innovators and startups.'),
('978-0520286044','Teaching in Senegalese Universities','Safiya Diallo',2,2015,4,4,'Academic practice and student engagement in Senegal.'),
('978-0143038328','The Beautyful Ones Are Not Yet Born','Ayi Kwei Armah',1,1968,4,4,'A novel about corruption, hope, and the search for dignity.'),
('978-1590173004','African Cities Reader','Joan Busquets',2,2011,4,4,'Urbanism, architecture, and city life in Africa.'),
('978-0134610993','Algorithms Unlocked','Thomas Cormen',3,2013,5,5,'Accessible exploration of how algorithms work.'),
('978-1472436588','Start-up Africa','Leila Abiola',4,2019,4,4,'Practical advice for new African entrepreneurs.'),
('978-1583673312','The Art of African Entrepreneurship','Kossi Kouassi',4,2016,4,4,'Strategies for building sustainable African businesses.'),
('978-1594036182','African Libraries and Digital Preservation','Robert Mwangi',2,2018,4,4,'Preserving knowledge in African digital archives.'),
('978-0143039608','Maru','Buchi Emecheta',1,1973,4,4,'A novel on love, education, and independence in Africa.'),
('978-0521666492','The Development of Higher Education in Senegal','Ndeye Sarr',2,2017,4,4,'How universities shaped modern Senegalese society.'),
('978-0321885375','Computer Networks','Andrew Tanenbaum',3,2011,4,4,'A strong foundation for networking principles.'),
('978-0131103627','The C Programming Language','Brian Kernighan',3,1988,4,4,'Classic technical reference for programming basics.'),
('978-1782256109','Design Thinking for African Innovators','Adama Cisse',4,2021,4,4,'Human-centered design for African technology projects.'),
('978-1478000220','Financial Inclusion in Africa','Aissatou Ba',4,2020,4,4,'How microfinance and fintech are changing economies.'),
('978-0520299791','The Liberated Library','Jean-Luc Mbaye',2,2016,4,4,'A study of public libraries as centers of knowledge and freedom.'),
('978-1783604989','African Digital Futures','Oumar Faye',3,2021,4,4,'Technology trends shaping the future of Africa.'),
('978-1780389274','Sustainable Business Growth in Africa','Maryam Touré',4,2019,4,4,'Business models for sustainable development in Africa.'),
('978-0812216070','The African Student Experience','Cheikh Fall',2,2014,4,4,'Voices from campuses across Senegal and West Africa.'),
('978-0136042594','Database Systems Concepts','Abraham Silberschatz',3,2018,4,4,'Fundamental database design using modern examples.'),
('978-1523081221','Emerging Technologies for Africa','Fatoumata Sarr',3,2022,5,5,'New technologies and their impact on African societies.'),
('978-1472409817','Culture and Education in Senegal','Mamadou Lamine',2,2013,4,4,'How educational values shape Senegalese youth.'),
('978-0812225682','African Storytelling','Ndiaye Boubacar',1,2015,5,5,'A collection of oral histories and cultural narratives.'),
('978-1984835522','Innovation Ecosystems in Africa','Khadija Sène',4,2020,4,4,'How innovation hubs and networks support African startups.'),
('978-1491978918','Python for Everybody','Charles Severance',3,2016,5,5,'A practical introduction to Python used in education worldwide.'),
('978-0521849397','The Atlantic World and Senegal','Aminata Coulibaly',2,2011,4,4,'History of Senegalese societies in the Atlantic era.'),
('978-1449369413','Visual Design for Web and Mobile','Jesmond Allen',3,2017,4,4,'Modern UI design principles for responsive digital products.'),
('978-0679771599','The African Child','Camara Laye',1,1990,4,4,'A classic coming-of-age story set in West Africa.'),
('978-1138071697','African Political Thought','Chinweizu',2,2013,4,4,'Essays exploring modern political ideas in Africa.'),
('978-0133591620','Programming PHP','Rasmus Lerdorf',3,2015,5,5,'A complete guide to PHP development with modern examples.'),
('978-1974303854','Mobile Money in Africa','Samuel Osei',4,2017,4,4,'How mobile payments transformed African commerce.'),
('978-1430241019','Human-Computer Interaction','Alan Dix',3,2015,4,4,'Design principles for usable applications and interfaces.'),
('978-1848115354','African Poetry and Culture','Mariam Diop',1,2016,4,4,'Exploration of contemporary African poetic voices.'),
('978-1784536467','Digital Literacy for Students','Binta Fall',3,2018,4,4,'Teaching practical digital skills for academic success.'),
('978-0199737022','The Rise of Senegalese Cinema','Abdoulaye Gueye',2,2014,4,4,'History of film and storytelling in Senegal.'),
('978-1472432207','The African Start-Up Economy','Kanesha N&apos;Dour',4,2018,4,4,'Case studies of startups growing in African markets.'),
('978-0199507502','Islam and Education in Senegal','Macky Sall',2,2010,4,4,'The relationship between faith and learning in Senegalese culture.'),
('978-1491935489','JavaScript & jQuery','Jon Duckett',3,2020,4,4,'Visual introduction to interactive web development.'),
('978-1474269405','African Digital Libraries','Moussa Dièye',2,2021,4,4,'Modern digital library systems for African campuses.'),
('978-0262033841','Deep Learning','Ian Goodfellow',3,2017,4,4,'A rigorous introduction to neural networks and AI.'),
('978-1982107778','The Future Is African','Adewale Adeyemi',4,2022,4,4,'Business strategies for a rising African market.'),
('978-1138713324','African Education in the 21st Century','Awa Ndiaye',2,2019,4,4,'Education policy and innovation for African students.'),
('978-1449355737','Design Patterns in PHP','William Sanders',3,2019,4,4,'Simplified software design patterns with PHP examples.'),
('978-1472425698','Funding African Innovation','Tamba Diallo',4,2020,4,4,'How funding and investment support African tech ecosystems.'),
('978-0262033841','Machine Learning','Tom Mitchell',3,1997,4,4,'A foundational text on classic machine learning algorithms.'),
('978-1789731576','Information Systems for Africa','Khadim Sow',3,2021,4,4,'Information system design for African organizations.'),
('978-0316537830','Memoirs of a Senegalese Library','Abdoulaye Ndoye',2,2018,4,4,'Personal memoirs reflecting on learning and libraries in Dakar.'),
('978-1138072518','The Most Beautiful Libraries of Africa','Lamine Thiam',2,2016,4,4,'A visual tour of inspiring library spaces across Africa.'),
('978-1491937161','Modern Responsive Web Design','David Powers',3,2018,4,4,'Responsive layout strategies using HTML5 and CSS3.'),
('978-0970486307','The African Entrepreneur','Kofi Boateng',4,2014,4,4,'Profiles of entrepreneurs shaping Africa&apos;s future.'),
('978-1788393128','Senegalese Art and Identity','Aminata Lo',1,2015,4,4,'Cultural essays on art, literature, and national identity.'),
('978-0128129722','Information Architecture for the Web','Louis Rosenfeld',3,2015,4,4,'Organizing digital content for better user experiences.'),
('978-1319247435','African Leadership in the 21st Century','Ndiaye Kante',4,2017,4,4,'Leadership strategies for modern African institutions.'),
('978-1034324387','Visual Storytelling for African Brands','Mame Diarra',4,2021,4,4,'Brand design and storytelling techniques for African companies.'),
('978-0321573513','The Pragmatic Programmer','Andrew Hunt',3,2019,4,4,'Core development habits for building effective software.'),
('978-0756698715','African Mythology','Stephen Belcher',1,2011,4,4,'A collection of myths from across the African continent.');

INSERT IGNORE INTO `borrowings` (`student_id`, `book_id`, `borrow_date`, `due_date`, `returned`, `returned_date`) VALUES
(1,1,'2026-05-15','2026-06-05',0,NULL),
(2,3,'2026-05-18','2026-06-08',0,NULL),
(3,12,'2026-05-20','2026-06-10',1,'2026-06-03'),
(4,8,'2026-05-22','2026-06-12',0,NULL),
(5,16,'2026-05-25','2026-06-14',1,'2026-06-04'),
(6,21,'2026-05-28','2026-06-18',0,NULL),
(7,29,'2026-05-30','2026-06-20',0,NULL),
(8,7,'2026-06-01','2026-06-21',0,NULL),
(9,15,'2026-06-02','2026-06-22',1,'2026-06-10'),
(10,24,'2026-06-04','2026-06-24',0,NULL);
