-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 23, 2024 at 06:47 PM
-- Server version: 10.6.16-MariaDB-0ubuntu0.22.04.1
-- PHP Version: 8.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `c1standa1`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`admin`@`localhost` PROCEDURE `UpdateLecturersFromUsers` ()  MODIFIES SQL DATA UPDATE lecturers
                                                                                                 SET
                                                                                                     first_name = (SELECT first_name FROM users WHERE teacherid = lecturers.id),
                                                                                                     last_name = (SELECT last_name FROM users WHERE teacherid = lecturers.id),
                                                                                                     picture_url = (SELECT picture_url FROM users WHERE teacherid = lecturers.id),
                                                                                                     membersince = (SELECT membersince FROM users WHERE teacherid = lecturers.id),

                                                                                                     mobilenumbers = (SELECT mobilenumbers FROM users WHERE teacherid = lecturers.id)
                                                                                                 WHERE lecturers.id IN (SELECT teacherid FROM users WHERE teacherid IS NOT NULL)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
                              `id` int(11) NOT NULL,
                              `uuid` text NOT NULL,
                              `isPublic` tinyint(1) NOT NULL DEFAULT 0,
                              `activityName` varchar(128) NOT NULL,
                              `description` varchar(256) NOT NULL,
                              `objectives` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`objectives`)),
                              `lengthMin` int(11) NOT NULL,
                              `lengthMax` int(11) NOT NULL,
                              `classStructure` text NOT NULL DEFAULT '[]',
                              `edLevel` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]',
                              `tools` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]',
                              `homePreparation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]',
                              `instructions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]',
                              `agenda` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]',
                              `links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]',
                              `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `uuid`, `isPublic`, `activityName`, `description`, `objectives`, `lengthMin`, `lengthMax`, `classStructure`, `edLevel`, `tools`, `homePreparation`, `instructions`, `agenda`, `links`, `gallery`) VALUES
                                                                                                                                                                                                                                      (1, 'ddf0e5a5-3a7d-45cd-a018-77c7a42bb583', 0, 'Test test', '[\"Vaření je základ\"]', '[\"Velice zajímavý cíly, ksdjflksjdfkljsd\"]', 10, 16, '[]', '[\"1. Stupeň\"]', '[\"nůžky\", \"propiska\"]', '[\"Nějaká ta domácí příprava\"]', '[\"Instrukce díky kterým něco dokážeš\"]', '[\"Agenda na dnešní den\"]', '[\"Odkazy\"]', '[\"GAlerie fotek\"]'),
                                                                                                                                                                                                                                      (2, 'ddf0e5a5-3a7d-45cd-a018-77c7a42bb535', 0, 'mam rad buchty', 'kolac mi spadl', '[\"Cíly\",\"cílylkdsjflksj\"]', 0, 0, '[]', '[\"secondarySchool\",\"secondarySchool\"]', '[\"kámen\",\"papír\"]', '[{\"title\":\"nejaky nazev\",\"warn\":\"varovani\",\"note\":\"nejaky poznamky\"},{\"title\":\"titul zajimavy\",\"warn\":\"varovani opjet\",\"note\":\"dalsi note\"}]', '[{\"title\":\"titulsky titul titulnaty\",\"warn\":\"lidi jsou zarivy\",\"note\":\"zapomnel jsem kolac\"},{\"title\":\"slunce je hoax\",\"warn\":\"dulezity error\",\"note\":\"poznamka pro tebe\"}]', '[{\"duration\":\"21\",\"title\":\"Soutez kolacu\",\"description\":\"Snist kolace\"},{\"duration\":\"30\",\"title\":\"Skakani pres zidle\",\"description\":\"skakani pres dreveny zidle\"}]', '[{\"url\":\"nejaky link\",\"title\":\"titul pro link\"},{\"url\":\"buchta s naplni\",\"title\":\"titul pro buchtu s naplni\"}]', '[{\"title\":\"prestizni titul\",\"images\":[{\"lowRes\":\"rozsireni zajimavy\",\"highRes\":\"nejlepsi rozsireni na svete\"},{\"lowRes\":\"nekdo se ztratil\",\"highRes\":\"sykory jsou smradlavy\"}]},{\"title\":\"letajici zbran\",\"images\":[{\"lowRes\":\"dulezity string\",\"highRes\":\"jeste dulezitejsi str\"},{\"lowRes\":\"nejnizsi rozsireni svjeta\",\"highRes\":\"rozs vyssi nez slunce\"}]}]'),
                                                                                                                                                                                                                                      (3, 'ddf0e5a5-3a7d-45cd-a018-77c7a42bb584', 0, 'Zábavná chemie', 'jak udělat hodiny zajímavějšími? Ukážeme si to na hodinách chemie', '[]', 10, 16, '[]', '[]', '[\"nlžky\", \"propiska\"]', '[]', '[]', '[]', '[]', '[]'),
                                                                                                                                                                                                                                      (4, 'ddf0e5a5-3a7d-45cd-a018-77c7a42bb584', 0, 'Zničí AI lidstvo?', 'někdo si myslí že AI zničí lidstvo, někdo ne, ale máme se vůbec bát? není AI dobrý pro průmysl?', '[]', 10, 16, '[]', '[]', '[\"nlžky\", \"propiska\"]', '[]', '[]', '[]', '[]', '[]'),
                                                                                                                                                                                                                                      (5, 'ddf0e5a5-3a7d-45cd-a018-77c7a42bb584', 0, 'AI JAKO NÁSTROJ PRO PROGRAMÁTORY', 'programátoři často využívají AI k programování, protože to je cool', '[]', 10, 16, '[]', '[]', '[\"nlžky\", \"propiska\"]', '[]', '[]', '[]', '[]', '[]'),
                                                                                                                                                                                                                                      (6, 'ddf0e5a5-3a7d-45cd-a018-77c7a42bb583', 0, 'hezke jmeno aktivity', 'velice dlouhy popis', '[\"cilyy1\",\"cillyyy2\"]', 0, 0, '[]', '[\"secondarySchool\",\"secondarySchool\"]', '[\"lupa\",\"orezavac\"]', '[{\"title\":\"titulssssss\",\"warn\":\"jkljsfklsjd\",\"note\":\"notysek tisek\"},{\"title\":\"tittttttul\",\"warn\":\"upozorneniiii\",\"note\":\"noooout\"}]', '[{\"title\":\"buchty jsou zkazene\",\"warn\":\"upozornenifsdf\",\"note\":\"cokoliv\"},{\"title\":\"tescomobile\",\"warn\":\"vodafonecz\",\"note\":\"kaktus\"}]', '[{\"duration\":\"20\",\"title\":\"synovec\",\"description\":\"jizda pro synovce\"},{\"duration\":\"35\",\"title\":\"triapul\",\"description\":\"april neni realny\"}]', '[{\"url\":\"odkaz rozumny\",\"title\":\"hlavni titul\"},{\"url\":\"otkas\",\"title\":\"tajtl\"}]', '[{\"title\":\"tajtlopjet\",\"images\":[{\"lowRes\":\"<lskdjflskdf>\",\"highRes\":\"vysokaresoluce\"},{\"lowRes\":\"veljice malares\",\"highRes\":\"opjetvjysokares\"}]},{\"title\":\"tajtul\",\"images\":[{\"lowRes\":\"uzel\",\"highRes\":\"uzeli\"},{\"lowRes\":\"nizkyresky\",\"highRes\":\"vyskyresky\"}]}]');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
                             `title_before` varchar(10) DEFAULT NULL,
                             `first_name` varchar(32) NOT NULL,
                             `middle_name` varchar(32) DEFAULT NULL,
                             `last_name` varchar(32) NOT NULL,
                             `title_after` varchar(10) DEFAULT NULL,
                             `bio` text DEFAULT NULL,
                             `links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
                             `picture_url` text DEFAULT NULL,
                             `claim` text DEFAULT NULL,
                             `price_per_hour` int(11) DEFAULT NULL,
                             `contact` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
                             `mobilenumbers` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
                             `emails` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
                             `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
                             `location` varchar(100) DEFAULT NULL,
                             `membersince` date DEFAULT NULL,
                             `reservations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`reservations`)),
                             `id` int(11) NOT NULL,
                             `uuid` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`title_before`, `first_name`, `middle_name`, `last_name`, `title_after`, `bio`, `links`, `picture_url`, `claim`, `price_per_hour`, `contact`, `mobilenumbers`, `emails`, `tags`, `location`, `membersince`, `reservations`, `id`, `uuid`) VALUES
                                                                                                                                                                                                                                                                       ('', 'Vítězslav', '', 'Kříž', '', 'Jsem vedoucí projektu Tour de App a student na Masarykově univerzitě v Brně. Baví mě organizovat projekty a věnovat se vývoji mobilních aplikací. Zkušenosti jsem získal vedoucí Tour de App a účastí v soutěžích jako Prezentiáda nebo pIšQworka. Věnuji se také dobrovolnictví, kde rozvíjím dovednosti v projektovém managementu a podporuji týmový úspěch. Momentálně se plně soustředím na vedoucí roli v Tour de App a studuji na univerzitě. Těším se na další výzvy a příležitosti pro osobní i profesní růst.', NULL, 'https://tourdeapp.cz/storage/images/2023_08_30/f68f19464293bd2d92b3d1dfe2a4e9e664ef5e6045096/big.png.webp', 'Vedoucí organizace soutěžních kol', 1300, NULL, '', 'kriz@scg.cz', '[{\"name\":\"MySQL\",\"uuid\":\"a9cf7dfd-5080-a410-7813-7fc7e2f4f858\"},{\"name\":\"Javascript\",\"uuid\":\"f0461fea-bca6-05cf-1832-150f3af66c73\"},{\"name\":\"Typescript\",\"uuid\":\"513b6405-78ba-e0db-0df0-64014e9532ec\"},{\"name\":\"Less\",\"uuid\":\"4d77b9cf-2062-6553-7f98-dbe9f5dfb0eb\"},{\"name\":\"Sass\",\"uuid\":\"c72073b9-4504-7ab5-946d-1aa119d7dcc1\"},{\"name\":\"PHP\",\"uuid\":\"38dcad78-f8f2-921c-657d-37b74d9587e9\"},{\"name\":\"React\",\"uuid\":\"c61eaea4-6a36-8411-f8a2-119a41c243f8\"},{\"name\":\"PostgreSQL\",\"uuid\":\"3b7baaae-ef2e-eca7-cf42-9882f6f428bf\"}]', 'Brno', '2023-12-30', '[{\"date\":\"2024-02-08 17:00:00\",\"user\":null,\"uuid\":\"88a46f32-da8d-409a-8ce0-412e23cb615b\"},{\"date\":\"2024-02-12 17:00:00\",\"user\":\"valek\",\"uuid\":\"871c63f1-416b-4de7-ac7e-419aaa2fb68d\"},{\"date\":\"2024-02-17 13:00:00\",\"user\":null,\"uuid\":\"670e5b43-bb4e-4174-8b8e-ed375c32df15\"},{\"date\":\"2024-02-12 10:00:00\",\"user\":null,\"uuid\":\"d7febbc6-d679-4c94-9b6e-14896c2dbfac\"},{\"date\":\"2024-02-22 15:00:00\",\"user\":\"chingun_sodoo\",\"uuid\":\"d7febbc6-d679-4c94-9b6e-14896c2dbfac\"},{\"date\":\"2024-03-05 15:00:00\",\"user\":\"testtest\",\"uuid\":\"940861f2-4a5e-eb3b-6563-8a4ce436abba\",\"user_first_name\":\"Test\",\"user_last_name\":\"testovy\",\"user_email\":\"test@test.test\",\"user_mobilenumbers\":[\"+420 56 4521 120\"]},{\"date\":\"2024-03-07 15:00:00\",\"user\":\"-\",\"uuid\":\"74af279a-231d-735b-1bbb-76f29e9335f2\",\"user_first_name\":\"Karel\",\"user_last_name\":\"Honsig\",\"user_email\":\"karel@honsig.eu\",\"user_mobilenumbers\":\"785565555\"},{\"date\":\"2024-03-16 18:00:00\",\"user\":null,\"uuid\":\"37ae3134-063f-5ced-7899-19caf0337142\"},{\"date\":\"2024-03-16 19:00:00\",\"user\":\"skudrna\",\"uuid\":\"55716b19-834d-5ea9-92b4-d92b28745f25\",\"user_email\":\"aldiix@email.cz\",\"user_last_name\":\"\\u0160kudrna\",\"user_first_name\":\"Stanislav\",\"user_mobilenumbers\":[\"+420 792 541 195\"]},{\"date\":\"2024-03-05 16:00:00\",\"user\":null,\"uuid\":\"459cb845-e5f3-a445-b347-ab81c377ae11\"},{\"date\":\"2024-02-26 16:00:00\",\"user\":null,\"uuid\":\"bdfa6fff-40b4-b491-91f8-18c4080c585d\"},{\"date\":\"2024-02-26 17:00:00\",\"user\":null,\"uuid\":\"d3b076a4-5cd2-0bde-639b-46bdb3f87e9b\"},{\"date\":\"2024-03-19 15:00:00\",\"user\":null,\"uuid\":\"ddb57362-50f3-fe4e-bdbe-f6137c9d82d1\"},{\"date\":\"2024-03-19 16:00:00\",\"user\":null,\"uuid\":\"659d8214-8b30-f8c7-1c17-262ec8000d06\"},{\"date\":\"2024-03-20 15:00:00\",\"user\":null,\"uuid\":\"878c16a1-b8b9-663b-24a7-fb0d95444500\"},{\"date\":\"2024-03-20 16:00:00\",\"user\":null,\"uuid\":\"c5351bbd-7f32-4a64-f58a-af63e2c01c64\"},{\"date\":\"2024-03-21 15:00:00\",\"user\":\"-\",\"uuid\":\"36ede30c-c64f-7f10-b163-20632d840078\",\"user_first_name\":null,\"user_last_name\":null,\"user_email\":null,\"user_mobilenumbers\":null},{\"date\":\"2024-03-21 16:00:00\",\"user\":null,\"uuid\":\"50d78688-7330-e553-ad8b-88089620cfae\"},{\"date\":\"2024-03-30 18:00:00\",\"user\":null,\"uuid\":\"2d1096f7-03e9-9e82-07a2-8806142162c8\"},{\"date\":\"2024-03-30 19:00:00\",\"user\":null,\"uuid\":\"42341694-0bbc-439c-a6e1-809e51f23aa9\"},{\"date\":\"2024-03-02 20:00:00\",\"user\":\"valek\",\"uuid\":\"41877670-182b-4b76-29ee-c86c0523ed4a\",\"user_email\":\"valekladislav@gmail.com\",\"user_last_name\":\"V\\u00e1lek\",\"user_first_name\":\"Ladislav\",\"user_mobilenumbers\":[\"+420 456 456 123\"]},{\"date\":\"2024-03-02 21:00:00\",\"user\":\"valek\",\"uuid\":\"dc8394bd-6f7b-51b0-7c55-f7ce3dd6d916\",\"user_email\":\"valekladislav@gmail.com\",\"user_last_name\":\"V\\u00e1lek\",\"user_first_name\":\"Ladislav\",\"user_mobilenumbers\":[\"+420 456 456 123\"]},{\"uuid\":\"5b978b55-f04f-e74a-f0b0-ff413debadd4\",\"date\":\"2024-03-06 20:00:00\",\"user\":\"valek\",\"user_first_name\":\"Ladislav\",\"user_last_name\":\"V\\u00e1lek\",\"user_email\":\"valekladislav@gmail.com\",\"user_mobilenumbers\":[\"+420 456 456 123\"]},{\"uuid\":\"5c609a4e-eef3-144e-3d6b-7d365cb4afa5\",\"date\":\"2024-02-27 18:00:00\",\"user\":null},{\"uuid\":\"472a0897-7a97-a1bc-b57e-5ccbc55ae6dd\",\"date\":\"2024-02-27 00:00:00\",\"user\":null},{\"uuid\":\"26cb5c24-2dbe-2fa6-7066-4bc5d31bb4e5\",\"date\":\"2024-02-28 18:00:00\",\"user\":\"testtest\",\"user_first_name\":\"Test\",\"user_last_name\":\"testovy\",\"user_email\":\"test@test.test\",\"user_mobilenumbers\":[\"+420 56 4521 120\"]},{\"uuid\":\"5aedf1fb-f433-b09f-e7e9-8dd75c878df9\",\"date\":\"2024-03-20 19:00:00\",\"user\":null}]', 1, '69758cd8-a626-4aba-bfd7-5f5bd2dfc555'),
                                                                                                                                                                                                                                                                       (NULL, 'Margareta', NULL, 'Verdyck', NULL, 'Jsem studentka na Masarykově univerzitě v Brně, kde studuji Pdf. Baví mě organizovat věci a věnovat se projektovému managementu. Získala jsem zkušenosti při vývoji mobilních aplikací ve společnosti Futured a účastnila jsem se různých soutěží, jako jsou Prezentiáda, pIšQworka nebo Tour de App.\r\nVěnuji se také dobrovolnictví a ráda předávám své znalosti dál. Mám radost z rozvíjení měkkých dovedností a vzdělávání. Momentálně se soustředím na své studium a těším se na další výzvy, které mi přinesou nové příležitosti k růstu a rozvoji.', NULL, 'https://tourdeapp.cz/storage/images/2023_02_09/f9314e418cf993776ddd241159a95e8f63e4cc0bc1593/big.png.webp', 'Grafická designerka', 1100, NULL, NULL, NULL, '[\n {\"name\":\"Dobrovolnictví\"},\n{\"name\":\"Prezentační dovednosti\"}\n]', 'Praha', NULL, NULL, 2, 'aadc2f67-6456-4910-91fb-617adf78418a'),
                                                                                                                                                                                                                                                                       ('Mgr.', 'Petra', 'Swil', 'Plachá', 'MBA', 'Baví mě organizovat věci. Ať už to bylo vyvíjení mobilních aplikací ve Futured, pořádání konferencí, spolupráce na soutěžích Prezentiáda, pIšQworky, Tour de App a Středoškolák roku, nebo třeba dobrovolnictví, vždycky jsem skončila u projektového managementu, rozvíjení soft-skills a vzdělávání. U studentských projektů a akcí jsem si vyzkoušela snad všechno od marketingu po logistiku a moc ráda to předám dál. Momentálně studuji Pdf MUNI a FF MUNI v Brně.', NULL, 'https://tourdeapp.cz/storage/images/2023_02_25/412ff296a291f021bbb6de10e8d0b94863fa89308843b/big.png.webp', 'Aktivní studentka / Předsedkyně spolku / Projektová manažerka', 1600, NULL, '+420 722 482 974', 'placha@scg.cz,predseda@scg.cz', '[\r\n{\"name\":\"Efektivní učení\"},\r\n{\"name\":\"Prezentační dovednosti\"},\r\n{\"name\":\"Marketing pro neziskové studentské projekty\"},\r\n{\"name\":\"Mimoškolní aktivity\"},\r\n{\"name\":\"Projektový management\"},\r\n{\"name\":\"Rvent management\"}\r\n]', 'Brno', NULL, NULL, 3, '41eb2a31-f129-4369-a7ac-8a26ffa10eeb'),
                                                                                                                                                                                                                                                                       ('Ing.', 'Jana', NULL, 'Málková', NULL, 'Ahoj, jsem Jana Málková a mé odborné zaměření se soustředí na C jazyky. Jsem vášnivá vývojářka, která se věnuje hloubkovému studiu a praktickému využití C jazyků. Mým cílem je nejen ovládat syntaxi a koncepty těchto jazyků, ale také chápat jejich interní mechaniky a optimálně je využívat pro efektivní programování. S radostí přijímám výzvy spojené s nízkoúrovňovým programováním a algoritmickým designem. Mé dovednosti v oblasti C jazyků rozšiřuji neustálým studiem a praktickým zapojením se do projektů, které zdůrazňují sílu a výkonnost těchto programovacích nástrojů.', NULL, 'https://images.pexels.com/photos/567458/pexels-photo-567458.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Programátorka v C jazycích', 900, NULL, '+420 605 481 102', 'janamalkova@seznam.cz', '[\r\n{\"name\":\"C++\"},\r\n{\"name\":\"C\"},\r\n{\"name\":\"C#\"},\r\n{\"name\":\"Java\"},\r\n{\"name\":\"Objective-C\"}\r\n]', 'Pardubice', '2024-01-15', NULL, 4, '621f773d-6cdd-466c-af60-bdb4c3e7f36d'),
                                                                                                                                                                                                                                                                       (NULL, 'Karel', NULL, 'Honsig', NULL, 'Jsem nadšený učitel odborných předmětů na Střední Škole Educhem. Vedle výuky se věnuji i studiu na vysoké škole a vášnivě programuji v PHP a Wordpressu. Mé zájmy sahají i do oblasti hardware a operačních systémů, což mě motivuje k neustálému profesnímu růstu a objevování nových výzev.', NULL, 'https://images.pexels.com/photos/1680175/pexels-photo-1680175.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'PHP programátor / Učitel odborných předmětů', 1500, NULL, NULL, 'karelhonsig@gmail.com', '[\r\n{\"name\":\"PHP\"},\r\n{\"name\":\"MySQL\"},\r\n{\"name\":\"Wordpress\"},\r\n{\"name\":\"Linux\"},\r\n{\"name\":\"Hardware\"}\r\n]', 'Most', '2024-01-15', NULL, 6, '145bcd65-a9df-491d-b541-2b4a92139294'),
                                                                                                                                                                                                                                                                       ('Ing.', 'Jakub', NULL, 'Havlíček', NULL, 'Jmenuju se Jakub Havlíček a mám tu čest pracovat pro společnost JetBrains s.r.o., kde se specializuji na vývoj integrovaných vývojových prostředí (IDE). Mým hlavním zaměřením je vytváření moderních nástrojů, které usnadňují a zefektivňují práci vývojářů. Mé dovednosti zahrnují širokou škálu technologií, od programovacích jazyků až po implementaci pokročilých funkcí v rámci IDE. S vášní přistupuji k tvorbě uživatelsky přívětivých prostředí a funkčností, které podporují produktivitu a kreativitu vývojářů. Je pro mě potěšením být součástí týmu, který přispívá k inovacím v oblasti vývoje softwaru.', NULL, 'https://images.pexels.com/photos/6613897/pexels-photo-6613897.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Programátor v JetBrains s.r.o', 2000, NULL, '+420 702 425 846,+420 601 105 501', 'jakubhavlicek@jetbrains.com,jhavlicek@gmail.com,jakhav@email.cz', '[\r\n{\"name\":\"Java\"},\r\n{\"name\":\"Kotlin\"},\r\n{\"name\":\"MySQL\"},\r\n{\"name\":\"PostgreSQL\"},\r\n{\"name\":\"C++\"},\r\n{\"name\":\"Python\"}\r\n]', 'Uherské Hradiště', '2024-01-15', NULL, 7, 'be2c66cc-7392-490e-a02c-b9783faac02b'),
                                                                                                                                                                                                                                                                       ('', 'Chingun', '', 'Sodoo', '', 'Jsem student na Střední škole Educhem, a mé vášně patří světu webového vývoje. Pracuji s programovacími jazyky jako HTML, CSS, JavaScript, C# a využívám také Sass pro efektivnější stylování. Moje projekty spojují technickou preciznost s kreativním designem, a s radostí se podílím na školních aktivitách, kde mohu sdílet své znalosti a přispívat k rozvoji technologického prostředí na naší škole.', NULL, '/images/avatars/383235651_783140936899145_8788747065482611390_n.jpg', 'Frontend developer / Student na SŠ Educhem', 1000, NULL, NULL, 'chingunsodoo786@gmail.com', '[{\"name\":\"HTML\",\"uuid\":\"863cecf0-c9a5-2168-b4f7-673cbca33c05\"},{\"name\":\"CSS\",\"uuid\":\"0c217e8d-705f-f1d6-e1ea-298a829238cd\"},{\"name\":\"Javascript\",\"uuid\":\"2902c14b-36b0-5e97-7901-bc552cbfbed7\"},{\"name\":\"SASS\",\"uuid\":\"442fe6f6-b468-5fc0-a49a-8fdc7a1dd809\"},{\"name\":\"C#\",\"uuid\":\"da1842db-fe44-5a7f-94ad-807645b4fdb0\"}]', 'Most', '2023-12-22', NULL, 40, '926318c3-d6c1-4a07-aab5-078902f6b9cb'),
                                                                                                                                                                                                                                                                       ('', 'Stanislav', '', 'Škudrna', '', 'Jsem student na Střední škole Educhem, a mé srdce patří světu backendového vývoje. Specializuji se na programovací jazyky jako C#, Java a PHP, a pracuji s databázovým systémem MySQL. Vytvářím robustní webové aplikace, kde kombinuji technologie jako Vue.js, HTML, CSS, SASS a JavaScript. Mé dovednosti nejsou pouze technické, ale i organizační – rád se zapojuji do školních projektů a sdílím své znalosti s ostatními studenty, abych přispěl k dynamickému technologickému prostředí na naší škole.', NULL, '/images/avatars/363810065_1034423334586363_5494913274458618812_n.jpg', 'Fullstack developer / Student na SŠ Educhem', 1500, NULL, '+420 792 541 195', 'stanislav.skudrna@email.cz,stanislavskudrna@email.cz', '[{\"name\":\"C#\",\"uuid\":\"1d17673f-0027-f76c-1ee9-dfc21116eccb\"},{\"name\":\"Java\",\"uuid\":\"6e126a51-e053-e70f-e0ac-b3b5d3da1fb5\"},{\"name\":\"Javascript\",\"uuid\":\"60d69ac4-5ab6-19a3-d842-8b19042564c5\"},{\"name\":\"HTML\",\"uuid\":\"d95a8d6f-6b9e-fa1e-8da8-25dce0d979de\"},{\"name\":\"CSS\",\"uuid\":\"79b4257e-b36c-025d-0f0f-8c5433bae961\"},{\"name\":\"SASS\",\"uuid\":\"2782e612-f7cf-43b4-7e95-dc01da5dc42f\"},{\"name\":\"PHP\",\"uuid\":\"576f77a7-7c98-e99e-b58e-b23611f1134e\"},{\"name\":\"MySQL\",\"uuid\":\"7735e833-71f6-e070-8ce5-154beb1cc755\"},{\"name\":\"Vuejs\",\"uuid\":\"25ad09f0-a1a1-a224-c904-cae5b500018c\"}]', 'Most', '2023-12-20', '[]', 41, '1349f656-8c9a-4052-8265-ca4e1033e237'),
                                                                                                                                                                                                                                                                       ('Ing. et Bc', 'Tomáš', NULL, 'Hudec', NULL, 'Jsem docent na Masarykově univerzitě, a mé odborné zájmy se zaměřují na široké pole programovacích jazyků a technologií, především v oblasti softwarového inženýrství a vývoje aplikací. Pracuji s backendovými jazyky a zkoumám pokročilé techniky v oblasti databázového designu. V mých hodinách se snažím nejen sdílet technické know-how, ale také podporovat rozvoj kreativity a analytického myšlení studentů. Aktivně se účastním odborných konferencí a projektů, abych přispěl k inovacím v oblasti softwarového inženýrství a přenášel aktuální trendy přímo do výuky na univerzitě.', NULL, 'https://images.pexels.com/photos/845457/pexels-photo-845457.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Docent na Masarykově Univerzitě', 2500, NULL, '+420 792 052 145', 't.hudec@gmail.com,hudec@email.cz', '[\r\n{\"name\":\"C#\"},\r\n{\"name\":\"Java\"},\r\n{\"name\":\"Python\"},\r\n{\"name\":\"Pascal\"},\r\n{\"name\":\"C\"},\r\n{\"name\":\"C++\"},\r\n{\"name\":\"Visual Basic\"},\r\n{\"name\":\"ASP.NET\"},\r\n{\"name\":\"PHP\"},\r\n{\"name\":\"F#\"}\r\n]', 'Praha', '2024-01-19', NULL, 42, 'e3295afc-8acc-424a-817b-b73241004e78'),
                                                                                                                                                                                                                                                                       ('Mgr.', 'Josef', NULL, 'Bednár', NULL, 'Zdravím, jmenuju se Josef Bednár, jsem sice v důchodu, ale s bohatým zázemím v oblasti programování. V mládí jsem se aktivně podílel na vývoji různých zařízení, které dnes již nejsou v běžném používání. Mým specializovaným dovednostem patří perfektní ovládání starých programovacích jazyků, které jsem využíval při vývoji těchto zařízení. S nadšením sdílím své zkušenosti a znalosti s ostatními, ačkoli nyní žiji v důchodu, stále mám vášeň pro historii programování a rád se věnuji retro technologiím.', NULL, 'https://images.pexels.com/photos/5264743/pexels-photo-5264743.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1', 'Programátor starých technologií', 800, NULL, NULL, 'bednar@seznam.cz', '[\r\n{\"name\":\"COBOL\"},\r\n{\"name\":\"BASIC\"},\r\n{\"name\":\"C\"},\r\n{\"name\":\"ML\"},\r\n{\"name\":\"Smalltalk\"},\r\n{\"name\":\"C++\"},\r\n{\"name\":\"SQL\"},\r\n{\"name\":\"Objective-C\"},\r\n{\"name\":\"Delphi\"}\r\n]', 'České Budějovice', '2024-01-19', NULL, 43, '62778b63-d34b-429b-ad42-9b0d2b9d156b'),
                                                                                                                                                                                                                                                                       ('', 'Ladislav', '', 'Válek', '', 'Čus všichni, jsem Ladislav Válek, jsem student a mé hlavní zaměření spočívá v oblasti backendového vývoje a správy databází. S vášní pracuji s různými backendovými jazyky, jako je C#, a databázovými systémy, včetně MySQL. Mé dovednosti zahrnují efektivní manipulaci s daty a vytváření robustních serverových aplikací. V rámci mého studia se snažím neustále zdokonalovat a sledovat aktuální trendy v oblasti backendového vývoje. Baví mě navrhovat a implementovat efektivní databázová řešení, a s radostí se podílím na projektech, které posilují mé dovednosti v této specializované oblasti.', NULL, 'https://images.pexels.com/photos/3141289/pexels-photo-3141289.jpeg?auto=compress&amp;cs=tinysrgb&amp;w=1260&amp;h=750&amp;dpr=1', 'Backend programátor / Student', 1300, NULL, '+420 456 456 123', 'valekladislav@gmail.com', '[{\"name\":\"Javascript\",\"uuid\":\"377f70f5-f75a-c584-03f0-dca6881b792b\"},{\"name\":\"Typescript\",\"uuid\":\"6be79b11-9a09-af3f-6de3-69be75dc5450\"},{\"name\":\"Java\",\"uuid\":\"a4bc5afb-ad29-64a3-9244-214d388baa25\"},{\"name\":\"Ruby\",\"uuid\":\"87dfb8af-5d2d-c541-9a8b-65c4d7eaf09e\"},{\"name\":\"PHP\",\"uuid\":\"a47444ff-a8cc-d35d-a161-30813a09e373\"},{\"name\":\"Python\",\"uuid\":\"b91d65e6-e243-3149-bbb4-5ad334d39e55\"},{\"name\":\"Perl\",\"uuid\":\"5447aa3f-daf8-8d76-5178-86e88a7cb661\"},{\"name\":\"C#\",\"uuid\":\"e1cb998e-54a5-cae0-7440-3783ea46abea\"},{\"name\":\"GO\",\"uuid\":\"163a2d07-5e98-1f1e-33cc-13eeb93217fe\"},{\"name\":\"MySQL\",\"uuid\":\"a50aae2e-f9e9-3913-8cad-4874b7724a0f\"},{\"name\":\"PostgreSQL\",\"uuid\":\"9345b978-24fd-9dca-c1c4-255b22557abe\"}]', 'Praha', '2024-01-19', NULL, 44, 'c8d90a54-1b9a-405e-99fc-28623b0d79ea');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
                         `username` varchar(50) NOT NULL,
                         `email` varchar(64) NOT NULL,
                         `mobilenumbers` text DEFAULT NULL,
                         `password` text NOT NULL,
                         `teacherid` int(11) DEFAULT NULL,
                         `first_name` varchar(32) CHARACTER SET utf32 COLLATE utf32_czech_ci DEFAULT NULL,
                         `last_name` varchar(32) CHARACTER SET utf32 COLLATE utf32_czech_ci DEFAULT NULL,
                         `gender` enum('MALE','FEMALE','OTHER') DEFAULT NULL,
                         `birthday` date DEFAULT NULL,
                         `picture_url` text DEFAULT NULL,
                         `reservations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`reservations`)),
                         `membersince` date DEFAULT NULL,
                         `adminaccount` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `email`, `mobilenumbers`, `password`, `teacherid`, `first_name`, `last_name`, `gender`, `birthday`, `picture_url`, `reservations`, `membersince`, `adminaccount`) VALUES
                                                                                                                                                                                                       ('admin', 'admin@admin.admin', NULL, 'a6f0743b3975cc224ae6b3b00b35fa363ab84add8d23da6970065e8ad19af9285d4f48cec67e84150bcab9a0a126fa76d96754233d0d4b4d93ffd013d34bcc3afec8f2a3f2e808ccb17c4d278b4fa469', NULL, '', '', NULL, NULL, '/images/avatars/default.svg', NULL, '2023-12-20', 1),
                                                                                                                                                                                                       ('chingun_sodoo', 'chingunsodoo@test.cz', NULL, '754521706a088367081f1d8c9c8980b8d5ae3848d20d956161297d3f38dd1b7ddff63311e6d35a01e605fb3b251716f5e734b43dd9d16a19cce5223818f653a26fd5bce0a0270467f657066323880d96', 40, 'Chingun', 'Sodoo', 'MALE', '2007-08-22', '/images/avatars/383235651_783140936899145_8788747065482611390_n.jpg', NULL, '2023-12-22', 1),
                                                                                                                                                                                                       ('pitner', 'jakub.pitner@educhem.cz', NULL, '3c4d22e96a8af7b8e9e42b336b0c55f2be5929ce9dab5e7b0805e352078eefda79c41f8576b281dfd3bd062e32fd77994001b3f79fbdd28edb7b08931c3aeb49789406d01073ca1782d86293dcfc0764', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-13', 0),
                                                                                                                                                                                                       ('skudrna', 'aldiix@email.cz', '+420 792 541 195', '1335f705a3c21a95090013303e731ad4c7a534c3edc5d3d4d30aad70f836536418b94206fc13a21ec323585500385bebb561c8f41465520f43a1a3d32e9b88ebbf0859200ad3b6515391e6926f2287da', 41, 'Stanislav', 'Škudrna', 'MALE', '2006-09-15', '/images/avatars/363810065_1034423334586363_5494913274458618812_n.jpg', NULL, '2023-12-20', 1),
                                                                                                                                                                                                       ('testtest', 'test@test.test', '+420 56 4521 120', '125d6d03b32c84d492747f79cf0bf6e179d287f341384eb5d6d3197525ad6be8e6df0116032935698f99a09e265073d1d6c32c274591bf1d0a20ad67cba921bc4d682ec4eed27c53849758bc13b6e179', NULL, 'Test', 'testovy', NULL, NULL, NULL, NULL, '2024-02-27', 0),
                                                                                                                                                                                                       ('tomas', 'tomas@gmail.com', NULL, 'a646cbcdd55eba8f23a7721413ee0c321bd9efbc8d212aede3276da1a12a4e73f61c9d01bc1edf2da4e60f1c86d3d7c16619f22d301667442fa7b6ab1a2a000d6a962563e235e1789e663e356ac8d9e4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-12-31', 0),
                                                                                                                                                                                                       ('valek', 'valekladislav@gmail.com', '+420 456 456 123', '65db693d2efa0ae7205f572e5da950129e83e39513a950d227c4f342cdb2416ce5540bb042458a9402747cd403cfca6dacfa2b5024b6a24959c66b93426dcf12b6d3c62ef814d572bab9c05db4aeb307', 44, 'Ladislav', 'Válek', NULL, NULL, 'https://images.pexels.com/photos/3141289/pexels-photo-3141289.jpeg?auto=compress&amp;cs=tinysrgb&amp;w=1260&amp;h=750&amp;dpr=1', NULL, '2024-01-19', 0),
                                                                                                                                                                                                       ('vitakriz', 'kriz@scg.cz', '', '5bb89643dd142eeb0093f1497b0f2dd683ef6005486f8a8a469c9539877338aa70e13beaecc3da218f62d7d350a32628c3c1e086adbf14d9ad9856cfd2c61fda5c00f42dbf08952753d3f83d28b3c156', 1, 'Vítězslav', 'Kříž', 'MALE', '2000-10-17', 'https://tourdeapp.cz/storage/images/2023_08_30/f68f19464293bd2d92b3d1dfe2a4e9e664ef5e6045096/big.png.webp', NULL, '2023-12-30', 0);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `sync s tabulkou lecturers` AFTER UPDATE ON `users` FOR EACH ROW IF NEW.first_name <> OLD.first_name OR NEW.last_name <> OLD.last_name OR NEW.teacherid <> OLD.teacherid OR NEW.picture_url <> OLD.picture_url OR NEW.membersince <> OLD.membersince OR NEW.mobilenumbers <> OLD.mobilenumbers THEN
        CALL UpdateLecturersFromUsers();
END IF
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
    ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
    ADD UNIQUE KEY `username` (`username`,`email`),
    ADD UNIQUE KEY `teacherid` (`teacherid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
