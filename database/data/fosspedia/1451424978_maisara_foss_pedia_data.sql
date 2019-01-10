-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 29, 2015 at 11:35 PM
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `foss_pedia`
--

--
-- Dumping data for table `ar_externallinks`
--

INSERT INTO `ar_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(1, 1, 0x2f2f6d6574612e77696b696d656469612e6f72672f77696b692f48656c703a436f6e74656e7473, 0x687474703a2f2f6f72672e77696b696d656469612e6d6574612e2f77696b692f48656c703a436f6e74656e7473);
INSERT INTO `ar_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(2, 1, 0x2f2f6d6574612e77696b696d656469612e6f72672f77696b692f48656c703a436f6e74656e7473, 0x68747470733a2f2f6f72672e77696b696d656469612e6d6574612e2f77696b692f48656c703a436f6e74656e7473);
INSERT INTO `ar_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(3, 1, 0x2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a436f6e66696775726174696f6e5f73657474696e6773, 0x687474703a2f2f6f72672e6d6564696177696b692e7777772e2f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a436f6e66696775726174696f6e5f73657474696e6773);
INSERT INTO `ar_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(4, 1, 0x2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a436f6e66696775726174696f6e5f73657474696e6773, 0x68747470733a2f2f6f72672e6d6564696177696b692e7777772e2f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a436f6e66696775726174696f6e5f73657474696e6773);
INSERT INTO `ar_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(5, 1, 0x2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a464151, 0x687474703a2f2f6f72672e6d6564696177696b692e7777772e2f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a464151);
INSERT INTO `ar_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(6, 1, 0x2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a464151, 0x68747470733a2f2f6f72672e6d6564696177696b692e7777772e2f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a464151);
INSERT INTO `ar_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(7, 1, 0x68747470733a2f2f6c697374732e77696b696d656469612e6f72672f6d61696c6d616e2f6c697374696e666f2f6d6564696177696b692d616e6e6f756e6365, 0x68747470733a2f2f6f72672e77696b696d656469612e6c697374732e2f6d61696c6d616e2f6c697374696e666f2f6d6564696177696b692d616e6e6f756e6365);

--
-- Dumping data for table `ar_interwiki`
--

INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('acronym', 0x687474703a2f2f7777772e6163726f6e796d66696e6465722e636f6d2f7e2f7365617263682f61662e617370783f737472696e673d6578616374264163726f6e796d3d2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('advogato', 0x687474703a2f2f7777772e6164766f6761746f2e6f72672f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('arxiv', 0x687474703a2f2f7777772e61727869762e6f72672f6162732f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('c2find', 0x687474703a2f2f63322e636f6d2f6367692f77696b693f46696e64506167652676616c75653d2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('cache', 0x687474703a2f2f7777772e676f6f676c652e636f6d2f7365617263683f713d63616368653a2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('commons', 0x68747470733a2f2f636f6d6d6f6e732e77696b696d656469612e6f72672f77696b692f2431, 0x68747470733a2f2f636f6d6d6f6e732e77696b696d656469612e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('dictionary', 0x687474703a2f2f7777772e646963742e6f72672f62696e2f446963743f44617461626173653d2a26466f726d3d44696374312653747261746567793d2a2651756572793d2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('docbook', 0x687474703a2f2f77696b692e646f63626f6f6b2e6f72672f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('doi', 0x687474703a2f2f64782e646f692e6f72672f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('drumcorpswiki', 0x687474703a2f2f7777772e6472756d636f72707377696b692e636f6d2f2431, 0x687474703a2f2f6472756d636f72707377696b692e636f6d2f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('dwjwiki', 0x687474703a2f2f7777772e737562657269632e6e65742f6367692d62696e2f64776a2f77696b692e6367693f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('elibre', 0x687474703a2f2f656e6369636c6f70656469612e75732e65732f696e6465782e7068702f2431, 0x687474703a2f2f656e6369636c6f70656469612e75732e65732f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('emacswiki', 0x687474703a2f2f7777772e656d61637377696b692e6f72672f6367692d62696e2f77696b692e706c3f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('foldoc', 0x687474703a2f2f666f6c646f632e6f72672f3f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('foxwiki', 0x687474703a2f2f666f782e77696b69732e636f6d2f77632e646c6c3f57696b697e2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('freebsdman', 0x687474703a2f2f7777772e467265654253442e6f72672f6367692f6d616e2e6367693f6170726f706f733d312671756572793d2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('gej', 0x687474703a2f2f7777772e6573706572616e746f2e64652f64656a2e6d616c6e6f76612f616b746976696b696f2e706c3f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('gentoo-wiki', 0x687474703a2f2f67656e746f6f2d77696b692e636f6d2f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('google', 0x687474703a2f2f7777772e676f6f676c652e636f6d2f7365617263683f713d2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('googlegroups', 0x687474703a2f2f67726f7570732e676f6f676c652e636f6d2f67726f7570733f713d2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('hammondwiki', 0x687474703a2f2f7777772e64616972696b692e6f72672f48616d6d6f6e6457696b692f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('hrwiki', 0x687474703a2f2f7777772e687277696b692e6f72672f77696b692f2431, 0x687474703a2f2f7777772e687277696b692e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('imdb', 0x687474703a2f2f7777772e696d64622e636f6d2f66696e643f713d24312674743d6f6e, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('jargonfile', 0x687474703a2f2f73756e69722e6f72672f617070732f6d6574612e706c3f77696b693d4a6172676f6e46696c652672656469726563743d2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('kmwiki', 0x687474703a2f2f6b6d77696b692e77696b697370616365732e636f6d2f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('linuxwiki', 0x687474703a2f2f6c696e757877696b692e64652f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('lojban', 0x687474703a2f2f7777772e6c6f6a62616e2e6f72672f74696b692f74696b692d696e6465782e7068703f706167653d2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('lqwiki', 0x687474703a2f2f77696b692e6c696e75787175657374696f6e732e6f72672f77696b692f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('lugkr', 0x687474703a2f2f7777772e6c75672d6b722e64652f77696b692f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('meatball', 0x687474703a2f2f7777772e7573656d6f642e636f6d2f6367692d62696e2f6d622e706c3f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('mediawikiwiki', 0x68747470733a2f2f7777772e6d6564696177696b692e6f72672f77696b692f2431, 0x68747470733a2f2f7777772e6d6564696177696b692e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('mediazilla', 0x68747470733a2f2f6275677a696c6c612e77696b696d656469612e6f72672f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('memoryalpha', 0x687474703a2f2f656e2e6d656d6f72792d616c7068612e6f72672f77696b692f2431, 0x687474703a2f2f656e2e6d656d6f72792d616c7068612e6f72672f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('metawiki', 0x687474703a2f2f73756e69722e6f72672f617070732f6d6574612e706c3f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('metawikimedia', 0x68747470733a2f2f6d6574612e77696b696d656469612e6f72672f77696b692f2431, 0x68747470733a2f2f6d6574612e77696b696d656469612e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('mozillawiki', 0x687474703a2f2f77696b692e6d6f7a696c6c612e6f72672f2431, 0x68747470733a2f2f77696b692e6d6f7a696c6c612e6f72672f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('mw', 0x68747470733a2f2f7777772e6d6564696177696b692e6f72672f77696b692f2431, 0x68747470733a2f2f7777772e6d6564696177696b692e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('oeis', 0x687474703a2f2f6f6569732e6f72672f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('openwiki', 0x687474703a2f2f6f70656e77696b692e636f6d2f6f772e6173703f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('ppr', 0x687474703a2f2f63322e636f6d2f6367692f77696b693f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('pythoninfo', 0x687474703a2f2f77696b692e707974686f6e2e6f72672f6d6f696e2f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('rfc', 0x687474703a2f2f7777772e7266632d656469746f722e6f72672f7266632f72666324312e747874, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('s23wiki', 0x687474703a2f2f7332332e6f72672f77696b692f2431, 0x687474703a2f2f7332332e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('seattlewireless', 0x687474703a2f2f73656174746c65776972656c6573732e6e65742f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('senseislibrary', 0x687474703a2f2f73656e736569732e786d702e6e65742f3f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('shoutwiki', 0x687474703a2f2f7777772e73686f757477696b692e636f6d2f77696b692f2431, 0x687474703a2f2f7777772e73686f757477696b692e636f6d2f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('sourceforge', 0x687474703a2f2f736f75726365666f7267652e6e65742f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('sourcewatch', 0x687474703a2f2f7777772e736f7572636577617463682e6f72672f696e6465782e7068703f7469746c653d2431, 0x687474703a2f2f7777772e736f7572636577617463682e6f72672f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('squeak', 0x687474703a2f2f77696b692e73717565616b2e6f72672f73717565616b2f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('tejo', 0x687474703a2f2f7777772e74656a6f2e6f72672f76696b696f2f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('theopedia', 0x687474703a2f2f7777772e7468656f70656469612e636f6d2f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('tmbw', 0x687474703a2f2f7777772e746d62772e6e65742f77696b692f2431, 0x687474703a2f2f746d62772e6e65742f77696b692f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('tmnet', 0x687474703a2f2f7777772e746563686e6f6d616e69666573746f732e6e65742f3f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('twiki', 0x687474703a2f2f7477696b692e6f72672f6367692d62696e2f766965772f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('uea', 0x687474703a2f2f7565612e6f72672f76696b696f2f696e6465782e7068702f2431, 0x687474703a2f2f7565612e6f72672f76696b696f2f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('uncyclopedia', 0x687474703a2f2f656e2e756e6379636c6f70656469612e636f2f77696b692f2431, 0x687474703a2f2f656e2e756e6379636c6f70656469612e636f2f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('unreal', 0x687474703a2f2f77696b692e6265796f6e64756e7265616c2e636f6d2f2431, 0x687474703a2f2f77696b692e6265796f6e64756e7265616c2e636f6d2f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('usemod', 0x687474703a2f2f7777772e7573656d6f642e636f6d2f6367692d62696e2f77696b692e706c3f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('webseitzwiki', 0x687474703a2f2f776562736569747a2e666c7578656e742e636f6d2f77696b692f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wiki', 0x687474703a2f2f63322e636f6d2f6367692f77696b693f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikia', 0x687474703a2f2f7777772e77696b69612e636f6d2f77696b692f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikibooks', 0x68747470733a2f2f656e2e77696b69626f6f6b732e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b69626f6f6b732e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikif1', 0x687474703a2f2f7777772e77696b6966312e6f72672f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikihow', 0x687474703a2f2f7777772e77696b69686f772e636f6d2f2431, 0x687474703a2f2f7777772e77696b69686f772e636f6d2f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikimedia', 0x68747470733a2f2f77696b696d65646961666f756e646174696f6e2e6f72672f77696b692f2431, 0x68747470733a2f2f77696b696d65646961666f756e646174696f6e2e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikinews', 0x68747470733a2f2f656e2e77696b696e6577732e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b696e6577732e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikinfo', 0x687474703a2f2f77696b696e666f2e636f2f456e676c6973682f696e6465782e7068702f2431, '', '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikipedia', 0x68747470733a2f2f656e2e77696b6970656469612e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b6970656469612e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikiquote', 0x68747470733a2f2f656e2e77696b6971756f74652e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b6971756f74652e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikisource', 0x68747470733a2f2f77696b69736f757263652e6f72672f77696b692f2431, 0x68747470733a2f2f77696b69736f757263652e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikispecies', 0x68747470733a2f2f737065636965732e77696b696d656469612e6f72672f77696b692f2431, 0x68747470733a2f2f737065636965732e77696b696d656469612e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikiversity', 0x68747470733a2f2f656e2e77696b69766572736974792e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b69766572736974792e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikivoyage', 0x68747470733a2f2f656e2e77696b69766f796167652e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b69766f796167652e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikt', 0x68747470733a2f2f656e2e77696b74696f6e6172792e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b74696f6e6172792e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `ar_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wiktionary', 0x68747470733a2f2f656e2e77696b74696f6e6172792e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b74696f6e6172792e6f72672f772f6170692e706870, '', 0, 0);

--
-- Dumping data for table `ar_job`
--

INSERT INTO `ar_job` (`job_id`, `job_cmd`, `job_namespace`, `job_title`, `job_timestamp`, `job_params`, `job_random`, `job_attempts`, `job_token`, `job_token_timestamp`, `job_sha1`) VALUES(1, 'recentChangesUpdate', -1, 'أحدث_التغييرات', '20151229205440', 0x613a313a7b733a343a2274797065223b733a353a227075726765223b7d, 878004249, 0, '', NULL, 'folj1ptaj4jxesizcnlzmdonv3vzhau');
INSERT INTO `ar_job` (`job_id`, `job_cmd`, `job_namespace`, `job_title`, `job_timestamp`, `job_params`, `job_random`, `job_attempts`, `job_token`, `job_token_timestamp`, `job_sha1`) VALUES(2, 'recentChangesUpdate', -1, 'أحدث_التغييرات', '20151229205440', 0x613a313a7b733a343a2274797065223b733a31313a226361636865557064617465223b7d, 771597399, 0, '', NULL, 'gfn547da8vyci5zo8e0r14e8482ihs4');

--
-- Dumping data for table `ar_msg_resource`
--

INSERT INTO `ar_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('user.options', 'ar', 0x7b7d, '20151229211227');
INSERT INTO `ar_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('user.tokens', 'ar', 0x7b7d, '20151229211227');

--
-- Dumping data for table `ar_page`
--

INSERT INTO `ar_page` (`page_id`, `page_namespace`, `page_title`, `page_restrictions`, `page_is_redirect`, `page_is_new`, `page_random`, `page_touched`, `page_links_updated`, `page_latest`, `page_len`, `page_content_model`, `page_lang`) VALUES(1, 0, 'الصفحة_الرئيسية', '', 0, 1, 0.591479383528, '20151229205440', '20151229205440', 1, 590, 'wikitext', NULL);

--
-- Dumping data for table `ar_recentchanges`
--

INSERT INTO `ar_recentchanges` (`rc_id`, `rc_timestamp`, `rc_user`, `rc_user_text`, `rc_namespace`, `rc_title`, `rc_comment`, `rc_minor`, `rc_bot`, `rc_new`, `rc_cur_id`, `rc_this_oldid`, `rc_last_oldid`, `rc_type`, `rc_source`, `rc_patrolled`, `rc_ip`, `rc_old_len`, `rc_new_len`, `rc_deleted`, `rc_logid`, `rc_log_type`, `rc_log_action`, `rc_params`) VALUES(1, '20151229205440', 0, 'MediaWiki default', 0, 'الصفحة_الرئيسية', '', 0, 0, 1, 1, 1, 0, 1, 'mw.new', 0, '127.0.0.1', 0, 590, 0, 0, NULL, '', '');

--
-- Dumping data for table `ar_revision`
--

INSERT INTO `ar_revision` (`rev_id`, `rev_page`, `rev_text_id`, `rev_comment`, `rev_user`, `rev_user_text`, `rev_timestamp`, `rev_minor_edit`, `rev_deleted`, `rev_len`, `rev_parent_id`, `rev_sha1`, `rev_content_model`, `rev_content_format`) VALUES(1, 1, 1, '', 0, 'MediaWiki default', '20151229205440', 0, 0, 590, 0, '0zb80rb7z88l27q0zf7cnsroe2z0lds', NULL, NULL);

--
-- Dumping data for table `ar_searchindex`
--

INSERT INTO `ar_searchindex` (`si_page`, `si_title`, `si_text`) VALUES(1, 'u8d8a7u8d984u8d8b5u8d981u8d8adu8d8a9 u8d8a7u8d984u8d8b1u8d8a6u8d98au8d8b3u8d98au8d8a9', '  u8d8aau8d985 u8d8aau8d8abu8d8a8u8d98au8d8aa u8d985u8d98au8d8afu8d98au8d8a7u8d988u8d98au8d983u8d98a u8d8a8u8d986u8d8acu8d8a7u8d8ad.  u8d8a7u8d8b3u8d8aau8d8b4u8d8b1 metau82ewikimediau82eorgu800 wiki help contents u8d8afu8d984u8d98au8d984 u8d8a7u8d984u8d985u8d8b3u8d8aau8d8aeu8d8afu8d985 u8d984u8d985u8d8b9u8d984u8d988u8d985u8d8a7u8d8aa u8d8adu8d988u8d984 u8d8a7u8d8b3u8d8aau8d8aeu8d8afu8d8a7u8d985 u8d8a8u8d8b1u8d986u8d8a7u8d985u8d8ac u8d8a7u8d984u8d988u8d98au8d983u8d98a. u8d8a7u8d984u8d8a8u8d8afu8d8a7u8d98au8d8a9 u8d8a7u8d984u8d8a8u8d8afu8d8a7u8d98au8d8a9 u8d8a7u8d984u8d8a8u8d8afu8d8a7u8d98au8d8a9 wwwu800u82emediawikiu82eorgu800 wiki special mylanguage manual configuration_settings u8d982u8d8a7u8d8a6u8d985u8d8a9 u8d8a5u8d8b9u8d8afu8d8a7u8d8afu8d8a7u8d8aa u8d8a7u8d984u8d8b6u8d8a8u8d8b7 wwwu800u82emediawikiu82eorgu800 wiki special mylanguage manual faqu800 u8d8a3u8d8b3u8d8a6u8d984u8d8a9 u8d985u8d8aau8d983u8d8b1u8d8b1u8d8a9 u8d8adu8d988u8d984 u8d985u8d98au8d8afu8d98au8d8a7u8d988u8d98au8d983u8d98a u8d8a7u8d984u8d982u8d8a7u8d8a6u8d985u8d8a9 u8d8a7u8d984u8d8a8u8d8b1u8d98au8d8afu8d98au8d8a9 u8d8a7u8d984u8d8aeu8d8a7u8d8b5u8d8a9 u8d8a8u8d8a5u8d8b5u8d8afu8d8a7u8d8b1 u8d985u8d98au8d8afu8d98au8d8a7u8d988u8d98au8d983u8d98a ');

--
-- Dumping data for table `ar_site_stats`
--

INSERT INTO `ar_site_stats` (`ss_row_id`, `ss_total_edits`, `ss_good_articles`, `ss_total_pages`, `ss_users`, `ss_active_users`, `ss_images`) VALUES(1, 1, 0, 1, 1, -1, 0);

--
-- Dumping data for table `ar_text`
--

INSERT INTO `ar_text` (`old_id`, `old_text`, `old_flags`) VALUES(1, 0x272727d8aad98520d8aad8abd8a8d98ad8aa20d985d98ad8afd98ad8a7d988d98ad983d98a20d8a8d986d8acd8a7d8ad2e2727270a0ad8a7d8b3d8aad8b4d8b1205b2f2f6d6574612e77696b696d656469612e6f72672f77696b692f48656c703a436f6e74656e747320d8afd984d98ad98420d8a7d984d985d8b3d8aad8aed8afd9855d20d984d985d8b9d984d988d985d8a7d8aa20d8add988d98420d8a7d8b3d8aad8aed8afd8a7d98520d8a8d8b1d986d8a7d985d8ac20d8a7d984d988d98ad983d98a2e0a0a3d3d20d8a7d984d8a8d8afd8a7d98ad8a9203d3d0a0a2a205b2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a436f6e66696775726174696f6e5f73657474696e677320d982d8a7d8a6d985d8a920d8a5d8b9d8afd8a7d8afd8a7d8aa20d8a7d984d8b6d8a8d8b75d0a2a205b2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a46415120d8a3d8b3d8a6d984d8a920d985d8aad983d8b1d8b1d8a920d8add988d98420d985d98ad8afd98ad8a7d988d98ad983d98a5d0a2a205b68747470733a2f2f6c697374732e77696b696d656469612e6f72672f6d61696c6d616e2f6c697374696e666f2f6d6564696177696b692d616e6e6f756e636520d8a7d984d982d8a7d8a6d985d8a920d8a7d984d8a8d8b1d98ad8afd98ad8a920d8a7d984d8aed8a7d8b5d8a920d8a8d8a5d8b5d8afd8a7d8b120d985d98ad8afd98ad8a7d988d98ad983d98a5d, 0x7574662d38);

--
-- Dumping data for table `ar_updatelog`
--

INSERT INTO `ar_updatelog` (`ul_key`, `ul_value`) VALUES('filearchive-fa_major_mime-patch-fa_major_mime-chemical.sql', NULL);
INSERT INTO `ar_updatelog` (`ul_key`, `ul_value`) VALUES('image-img_major_mime-patch-img_major_mime-chemical.sql', NULL);
INSERT INTO `ar_updatelog` (`ul_key`, `ul_value`) VALUES('oldimage-oi_major_mime-patch-oi_major_mime-chemical.sql', NULL);
INSERT INTO `ar_updatelog` (`ul_key`, `ul_value`) VALUES('user_former_groups-ufg_group-patch-ufg_group-length-increase-255.sql', NULL);
INSERT INTO `ar_updatelog` (`ul_key`, `ul_value`) VALUES('user_groups-ug_group-patch-ug_group-length-increase-255.sql', NULL);
INSERT INTO `ar_updatelog` (`ul_key`, `ul_value`) VALUES('user_properties-up_property-patch-up_property.sql', NULL);

--
-- Dumping data for table `ar_user`
--

INSERT INTO `ar_user` (`user_id`, `user_name`, `user_real_name`, `user_password`, `user_newpassword`, `user_newpass_time`, `user_email`, `user_touched`, `user_token`, `user_email_authenticated`, `user_email_token`, `user_email_token_expires`, `user_registration`, `user_editcount`, `user_password_expires`) VALUES(1, 'Foss', 'foss', 0x3a70626b6466323a7368613235363a31303030303a3132383a7376594f5a6d5555752f54525046794b546e487032673d3d3a64377637472f4e4575514b572f4158794a494a67312b5656695563414956744a4e34656648667a487a535875417a623636444236584a37614765797a6f71362b5163516973524e3469334a366c4b654470382f667765466c483144436862724d7632734e7032524942314d6733325669762b72484e477a466d65434d526f316d427153696b6467364243466f416f76533775696b49456a4d64316a385151535a452f502b505a70553866303d, '', NULL, 'yomna.fahmy@espace.com.eg', '20151229211231', '0204ee217237c986bf40397c6caf068b', NULL, NULL, NULL, '20151229205439', 0, NULL);

--
-- Dumping data for table `ar_user_groups`
--

INSERT INTO `ar_user_groups` (`ug_user`, `ug_group`) VALUES(1, 'bureaucrat');
INSERT INTO `ar_user_groups` (`ug_user`, `ug_group`) VALUES(1, 'sysop');

--
-- Dumping data for table `en_externallinks`
--

INSERT INTO `en_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(1, 1, 0x2f2f6d6574612e77696b696d656469612e6f72672f77696b692f48656c703a436f6e74656e7473, 0x687474703a2f2f6f72672e77696b696d656469612e6d6574612e2f77696b692f48656c703a436f6e74656e7473);
INSERT INTO `en_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(2, 1, 0x2f2f6d6574612e77696b696d656469612e6f72672f77696b692f48656c703a436f6e74656e7473, 0x68747470733a2f2f6f72672e77696b696d656469612e6d6574612e2f77696b692f48656c703a436f6e74656e7473);
INSERT INTO `en_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(3, 1, 0x2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a436f6e66696775726174696f6e5f73657474696e6773, 0x687474703a2f2f6f72672e6d6564696177696b692e7777772e2f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a436f6e66696775726174696f6e5f73657474696e6773);
INSERT INTO `en_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(4, 1, 0x2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a436f6e66696775726174696f6e5f73657474696e6773, 0x68747470733a2f2f6f72672e6d6564696177696b692e7777772e2f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a436f6e66696775726174696f6e5f73657474696e6773);
INSERT INTO `en_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(5, 1, 0x2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a464151, 0x687474703a2f2f6f72672e6d6564696177696b692e7777772e2f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a464151);
INSERT INTO `en_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(6, 1, 0x2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a464151, 0x68747470733a2f2f6f72672e6d6564696177696b692e7777772e2f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a464151);
INSERT INTO `en_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(7, 1, 0x68747470733a2f2f6c697374732e77696b696d656469612e6f72672f6d61696c6d616e2f6c697374696e666f2f6d6564696177696b692d616e6e6f756e6365, 0x68747470733a2f2f6f72672e77696b696d656469612e6c697374732e2f6d61696c6d616e2f6c697374696e666f2f6d6564696177696b692d616e6e6f756e6365);
INSERT INTO `en_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(8, 1, 0x2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4c6f63616c69736174696f6e235472616e736c6174696f6e5f7265736f7572636573, 0x687474703a2f2f6f72672e6d6564696177696b692e7777772e2f77696b692f5370656369616c3a4d794c616e67756167652f4c6f63616c69736174696f6e235472616e736c6174696f6e5f7265736f7572636573);
INSERT INTO `en_externallinks` (`el_id`, `el_from`, `el_to`, `el_index`) VALUES(9, 1, 0x2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4c6f63616c69736174696f6e235472616e736c6174696f6e5f7265736f7572636573, 0x68747470733a2f2f6f72672e6d6564696177696b692e7777772e2f77696b692f5370656369616c3a4d794c616e67756167652f4c6f63616c69736174696f6e235472616e736c6174696f6e5f7265736f7572636573);

--
-- Dumping data for table `en_interwiki`
--

INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('acronym', 0x687474703a2f2f7777772e6163726f6e796d66696e6465722e636f6d2f7e2f7365617263682f61662e617370783f737472696e673d6578616374264163726f6e796d3d2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('advogato', 0x687474703a2f2f7777772e6164766f6761746f2e6f72672f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('arxiv', 0x687474703a2f2f7777772e61727869762e6f72672f6162732f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('c2find', 0x687474703a2f2f63322e636f6d2f6367692f77696b693f46696e64506167652676616c75653d2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('cache', 0x687474703a2f2f7777772e676f6f676c652e636f6d2f7365617263683f713d63616368653a2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('commons', 0x68747470733a2f2f636f6d6d6f6e732e77696b696d656469612e6f72672f77696b692f2431, 0x68747470733a2f2f636f6d6d6f6e732e77696b696d656469612e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('dictionary', 0x687474703a2f2f7777772e646963742e6f72672f62696e2f446963743f44617461626173653d2a26466f726d3d44696374312653747261746567793d2a2651756572793d2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('docbook', 0x687474703a2f2f77696b692e646f63626f6f6b2e6f72672f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('doi', 0x687474703a2f2f64782e646f692e6f72672f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('drumcorpswiki', 0x687474703a2f2f7777772e6472756d636f72707377696b692e636f6d2f2431, 0x687474703a2f2f6472756d636f72707377696b692e636f6d2f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('dwjwiki', 0x687474703a2f2f7777772e737562657269632e6e65742f6367692d62696e2f64776a2f77696b692e6367693f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('elibre', 0x687474703a2f2f656e6369636c6f70656469612e75732e65732f696e6465782e7068702f2431, 0x687474703a2f2f656e6369636c6f70656469612e75732e65732f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('emacswiki', 0x687474703a2f2f7777772e656d61637377696b692e6f72672f6367692d62696e2f77696b692e706c3f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('foldoc', 0x687474703a2f2f666f6c646f632e6f72672f3f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('foxwiki', 0x687474703a2f2f666f782e77696b69732e636f6d2f77632e646c6c3f57696b697e2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('freebsdman', 0x687474703a2f2f7777772e467265654253442e6f72672f6367692f6d616e2e6367693f6170726f706f733d312671756572793d2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('gej', 0x687474703a2f2f7777772e6573706572616e746f2e64652f64656a2e6d616c6e6f76612f616b746976696b696f2e706c3f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('gentoo-wiki', 0x687474703a2f2f67656e746f6f2d77696b692e636f6d2f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('google', 0x687474703a2f2f7777772e676f6f676c652e636f6d2f7365617263683f713d2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('googlegroups', 0x687474703a2f2f67726f7570732e676f6f676c652e636f6d2f67726f7570733f713d2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('hammondwiki', 0x687474703a2f2f7777772e64616972696b692e6f72672f48616d6d6f6e6457696b692f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('hrwiki', 0x687474703a2f2f7777772e687277696b692e6f72672f77696b692f2431, 0x687474703a2f2f7777772e687277696b692e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('imdb', 0x687474703a2f2f7777772e696d64622e636f6d2f66696e643f713d24312674743d6f6e, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('jargonfile', 0x687474703a2f2f73756e69722e6f72672f617070732f6d6574612e706c3f77696b693d4a6172676f6e46696c652672656469726563743d2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('kmwiki', 0x687474703a2f2f6b6d77696b692e77696b697370616365732e636f6d2f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('linuxwiki', 0x687474703a2f2f6c696e757877696b692e64652f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('lojban', 0x687474703a2f2f7777772e6c6f6a62616e2e6f72672f74696b692f74696b692d696e6465782e7068703f706167653d2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('lqwiki', 0x687474703a2f2f77696b692e6c696e75787175657374696f6e732e6f72672f77696b692f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('lugkr', 0x687474703a2f2f7777772e6c75672d6b722e64652f77696b692f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('meatball', 0x687474703a2f2f7777772e7573656d6f642e636f6d2f6367692d62696e2f6d622e706c3f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('mediawikiwiki', 0x68747470733a2f2f7777772e6d6564696177696b692e6f72672f77696b692f2431, 0x68747470733a2f2f7777772e6d6564696177696b692e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('mediazilla', 0x68747470733a2f2f6275677a696c6c612e77696b696d656469612e6f72672f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('memoryalpha', 0x687474703a2f2f656e2e6d656d6f72792d616c7068612e6f72672f77696b692f2431, 0x687474703a2f2f656e2e6d656d6f72792d616c7068612e6f72672f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('metawiki', 0x687474703a2f2f73756e69722e6f72672f617070732f6d6574612e706c3f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('metawikimedia', 0x68747470733a2f2f6d6574612e77696b696d656469612e6f72672f77696b692f2431, 0x68747470733a2f2f6d6574612e77696b696d656469612e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('mozillawiki', 0x687474703a2f2f77696b692e6d6f7a696c6c612e6f72672f2431, 0x68747470733a2f2f77696b692e6d6f7a696c6c612e6f72672f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('mw', 0x68747470733a2f2f7777772e6d6564696177696b692e6f72672f77696b692f2431, 0x68747470733a2f2f7777772e6d6564696177696b692e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('oeis', 0x687474703a2f2f6f6569732e6f72672f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('openwiki', 0x687474703a2f2f6f70656e77696b692e636f6d2f6f772e6173703f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('ppr', 0x687474703a2f2f63322e636f6d2f6367692f77696b693f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('pythoninfo', 0x687474703a2f2f77696b692e707974686f6e2e6f72672f6d6f696e2f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('rfc', 0x687474703a2f2f7777772e7266632d656469746f722e6f72672f7266632f72666324312e747874, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('s23wiki', 0x687474703a2f2f7332332e6f72672f77696b692f2431, 0x687474703a2f2f7332332e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('seattlewireless', 0x687474703a2f2f73656174746c65776972656c6573732e6e65742f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('senseislibrary', 0x687474703a2f2f73656e736569732e786d702e6e65742f3f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('shoutwiki', 0x687474703a2f2f7777772e73686f757477696b692e636f6d2f77696b692f2431, 0x687474703a2f2f7777772e73686f757477696b692e636f6d2f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('sourceforge', 0x687474703a2f2f736f75726365666f7267652e6e65742f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('sourcewatch', 0x687474703a2f2f7777772e736f7572636577617463682e6f72672f696e6465782e7068703f7469746c653d2431, 0x687474703a2f2f7777772e736f7572636577617463682e6f72672f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('squeak', 0x687474703a2f2f77696b692e73717565616b2e6f72672f73717565616b2f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('tejo', 0x687474703a2f2f7777772e74656a6f2e6f72672f76696b696f2f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('theopedia', 0x687474703a2f2f7777772e7468656f70656469612e636f6d2f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('tmbw', 0x687474703a2f2f7777772e746d62772e6e65742f77696b692f2431, 0x687474703a2f2f746d62772e6e65742f77696b692f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('tmnet', 0x687474703a2f2f7777772e746563686e6f6d616e69666573746f732e6e65742f3f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('twiki', 0x687474703a2f2f7477696b692e6f72672f6367692d62696e2f766965772f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('uea', 0x687474703a2f2f7565612e6f72672f76696b696f2f696e6465782e7068702f2431, 0x687474703a2f2f7565612e6f72672f76696b696f2f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('uncyclopedia', 0x687474703a2f2f656e2e756e6379636c6f70656469612e636f2f77696b692f2431, 0x687474703a2f2f656e2e756e6379636c6f70656469612e636f2f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('unreal', 0x687474703a2f2f77696b692e6265796f6e64756e7265616c2e636f6d2f2431, 0x687474703a2f2f77696b692e6265796f6e64756e7265616c2e636f6d2f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('usemod', 0x687474703a2f2f7777772e7573656d6f642e636f6d2f6367692d62696e2f77696b692e706c3f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('webseitzwiki', 0x687474703a2f2f776562736569747a2e666c7578656e742e636f6d2f77696b692f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wiki', 0x687474703a2f2f63322e636f6d2f6367692f77696b693f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikia', 0x687474703a2f2f7777772e77696b69612e636f6d2f77696b692f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikibooks', 0x68747470733a2f2f656e2e77696b69626f6f6b732e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b69626f6f6b732e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikif1', 0x687474703a2f2f7777772e77696b6966312e6f72672f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikihow', 0x687474703a2f2f7777772e77696b69686f772e636f6d2f2431, 0x687474703a2f2f7777772e77696b69686f772e636f6d2f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikimedia', 0x68747470733a2f2f77696b696d65646961666f756e646174696f6e2e6f72672f77696b692f2431, 0x68747470733a2f2f77696b696d65646961666f756e646174696f6e2e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikinews', 0x68747470733a2f2f656e2e77696b696e6577732e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b696e6577732e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikinfo', 0x687474703a2f2f77696b696e666f2e636f2f456e676c6973682f696e6465782e7068702f2431, '', '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikipedia', 0x68747470733a2f2f656e2e77696b6970656469612e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b6970656469612e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikiquote', 0x68747470733a2f2f656e2e77696b6971756f74652e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b6971756f74652e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikisource', 0x68747470733a2f2f77696b69736f757263652e6f72672f77696b692f2431, 0x68747470733a2f2f77696b69736f757263652e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikispecies', 0x68747470733a2f2f737065636965732e77696b696d656469612e6f72672f77696b692f2431, 0x68747470733a2f2f737065636965732e77696b696d656469612e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikiversity', 0x68747470733a2f2f656e2e77696b69766572736974792e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b69766572736974792e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikivoyage', 0x68747470733a2f2f656e2e77696b69766f796167652e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b69766f796167652e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wikt', 0x68747470733a2f2f656e2e77696b74696f6e6172792e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b74696f6e6172792e6f72672f772f6170692e706870, '', 0, 0);
INSERT INTO `en_interwiki` (`iw_prefix`, `iw_url`, `iw_api`, `iw_wikiid`, `iw_local`, `iw_trans`) VALUES('wiktionary', 0x68747470733a2f2f656e2e77696b74696f6e6172792e6f72672f77696b692f2431, 0x68747470733a2f2f656e2e77696b74696f6e6172792e6f72672f772f6170692e706870, '', 0, 0);

--
-- Dumping data for table `en_module_deps`
--

INSERT INTO `en_module_deps` (`md_module`, `md_skin`, `md_deps`) VALUES('mediawiki.ui.button', 'mediawikibootstrap', 0x5b222f7573722f73686172652f6e67696e782f68746d6c2f6567666f73732f6567797074666f73732f77696b692f7265736f75726365732f7372632f6d6564696177696b692e75692f636f6d706f6e656e74732f627574746f6e732e6c657373222c222f7573722f73686172652f6e67696e782f68746d6c2f6567666f73732f6567797074666f73732f77696b692f7265736f75726365732f7372632f6d6564696177696b692e6c6573732f6d6564696177696b692e6d6978696e732e6c657373222c222f7573722f73686172652f6e67696e782f68746d6c2f6567666f73732f6567797074666f73732f77696b692f7265736f75726365732f7372632f6d6564696177696b692e6c6573732f6d6564696177696b692e75692f7661726961626c65732e6c657373222c222f7573722f73686172652f6e67696e782f68746d6c2f6567666f73732f6567797074666f73732f77696b692f7265736f75726365732f7372632f6d6564696177696b692e6c6573732f6d6564696177696b692e75692f6d6978696e732e6c657373225d);

--
-- Dumping data for table `en_msg_resource`
--

INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('jquery.accessKeyLabel', 'ar', 0x7b22627261636b657473223a225b24315d222c22776f72642d736570617261746f72223a2220227d, '20151229211230');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('jquery.accessKeyLabel', 'en', 0x7b22627261636b657473223a225b24315d222c22776f72642d736570617261746f72223a2220227d, '20151229212232');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('jquery.client', 'ar', 0x7b7d, '20151229211230');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('jquery.client', 'en', 0x7b7d, '20151229212232');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('jquery.mwExtension', 'ar', 0x7b7d, '20151229211230');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('jquery.mwExtension', 'en', 0x7b7d, '20151229212232');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('mediawiki.legacy.ajax', 'ar', 0x7b7d, '20151229211230');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('mediawiki.legacy.ajax', 'en', 0x7b7d, '20151229212232');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('mediawiki.legacy.wikibits', 'ar', 0x7b7d, '20151229211230');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('mediawiki.legacy.wikibits', 'en', 0x7b7d, '20151229212232');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('mediawiki.notify', 'ar', 0x7b7d, '20151229211230');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('mediawiki.notify', 'en', 0x7b7d, '20151229212232');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('mediawiki.page.startup', 'ar', 0x7b7d, '20151229211230');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('mediawiki.page.startup', 'en', 0x7b7d, '20151229212232');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('mediawiki.util', 'ar', 0x7b7d, '20151229211230');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('mediawiki.util', 'en', 0x7b7d, '20151229212232');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('user.options', 'en', 0x7b7d, '20151229212232');
INSERT INTO `en_msg_resource` (`mr_resource`, `mr_lang`, `mr_blob`, `mr_timestamp`) VALUES('user.tokens', 'en', 0x7b7d, '20151229212232');

--
-- Dumping data for table `en_msg_resource_links`
--

INSERT INTO `en_msg_resource_links` (`mrl_resource`, `mrl_message`) VALUES('jquery.accessKeyLabel', 'brackets');
INSERT INTO `en_msg_resource_links` (`mrl_resource`, `mrl_message`) VALUES('jquery.accessKeyLabel', 'word-separator');

--
-- Dumping data for table `en_page`
--

INSERT INTO `en_page` (`page_id`, `page_namespace`, `page_title`, `page_restrictions`, `page_is_redirect`, `page_is_new`, `page_random`, `page_touched`, `page_links_updated`, `page_latest`, `page_len`, `page_content_model`, `page_lang`) VALUES(1, 0, 'Main_Page', '', 0, 1, 0.910590475911, '20151229205007', '20151229205007', 1, 592, 'wikitext', NULL);

--
-- Dumping data for table `en_querycache_info`
--

INSERT INTO `en_querycache_info` (`qci_type`, `qci_timestamp`) VALUES('activeusers', '20151229211156');

--
-- Dumping data for table `en_recentchanges`
--

INSERT INTO `en_recentchanges` (`rc_id`, `rc_timestamp`, `rc_user`, `rc_user_text`, `rc_namespace`, `rc_title`, `rc_comment`, `rc_minor`, `rc_bot`, `rc_new`, `rc_cur_id`, `rc_this_oldid`, `rc_last_oldid`, `rc_type`, `rc_source`, `rc_patrolled`, `rc_ip`, `rc_old_len`, `rc_new_len`, `rc_deleted`, `rc_logid`, `rc_log_type`, `rc_log_action`, `rc_params`) VALUES(1, '20151229205007', 0, 'MediaWiki default', 0, 'Main_Page', '', 0, 0, 1, 1, 1, 0, 1, 'mw.new', 0, '127.0.0.1', 0, 592, 0, 0, NULL, '', '');

--
-- Dumping data for table `en_revision`
--

INSERT INTO `en_revision` (`rev_id`, `rev_page`, `rev_text_id`, `rev_comment`, `rev_user`, `rev_user_text`, `rev_timestamp`, `rev_minor_edit`, `rev_deleted`, `rev_len`, `rev_parent_id`, `rev_sha1`, `rev_content_model`, `rev_content_format`) VALUES(1, 1, 1, '', 0, 'MediaWiki default', '20151229205007', 0, 0, 592, 0, 'glba3g2evzm40dqnqxegze66eqibkvb', NULL, NULL);

--
-- Dumping data for table `en_searchindex`
--

INSERT INTO `en_searchindex` (`si_page`, `si_title`, `si_text`) VALUES(1, 'main page', ' mediawiki hasu800 been successfully installed. consult theu800 metau82ewikimediau82eorgu800 wiki help contents user user''su800 guide foru800 information onu800 using theu800 wiki software. getting started getting started getting started wwwu800u82emediawikiu82eorgu800 wiki special mylanguage manual configuration_settings configuration settings list wwwu800u82emediawikiu82eorgu800 wiki special mylanguage manual faqu800 mediawiki faqu800 mediawiki release mailing list wwwu800u82emediawikiu82eorgu800 wiki special mylanguage localisation#translation_resources localise mediawiki foru800 your language ');

--
-- Dumping data for table `en_site_stats`
--

INSERT INTO `en_site_stats` (`ss_row_id`, `ss_total_edits`, `ss_good_articles`, `ss_total_pages`, `ss_users`, `ss_active_users`, `ss_images`) VALUES(1, 1, 0, 1, 1, -1, 0);

--
-- Dumping data for table `en_text`
--

INSERT INTO `en_text` (`old_id`, `old_text`, `old_flags`) VALUES(1, 0x3c7374726f6e673e4d6564696157696b6920686173206265656e207375636365737366756c6c7920696e7374616c6c65642e3c2f7374726f6e673e0a0a436f6e73756c7420746865205b2f2f6d6574612e77696b696d656469612e6f72672f77696b692f48656c703a436f6e74656e7473205573657227732047756964655d20666f7220696e666f726d6174696f6e206f6e207573696e67207468652077696b6920736f6674776172652e0a0a3d3d2047657474696e672073746172746564203d3d0a2a205b2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a436f6e66696775726174696f6e5f73657474696e677320436f6e66696775726174696f6e2073657474696e6773206c6973745d0a2a205b2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4d616e75616c3a464151204d6564696157696b69204641515d0a2a205b68747470733a2f2f6c697374732e77696b696d656469612e6f72672f6d61696c6d616e2f6c697374696e666f2f6d6564696177696b692d616e6e6f756e6365204d6564696157696b692072656c65617365206d61696c696e67206c6973745d0a2a205b2f2f7777772e6d6564696177696b692e6f72672f77696b692f5370656369616c3a4d794c616e67756167652f4c6f63616c69736174696f6e235472616e736c6174696f6e5f7265736f7572636573204c6f63616c697365204d6564696157696b6920666f7220796f7572206c616e67756167655d, 0x7574662d38);

--
-- Dumping data for table `en_updatelog`
--

INSERT INTO `en_updatelog` (`ul_key`, `ul_value`) VALUES('filearchive-fa_major_mime-patch-fa_major_mime-chemical.sql', NULL);
INSERT INTO `en_updatelog` (`ul_key`, `ul_value`) VALUES('image-img_major_mime-patch-img_major_mime-chemical.sql', NULL);
INSERT INTO `en_updatelog` (`ul_key`, `ul_value`) VALUES('oldimage-oi_major_mime-patch-oi_major_mime-chemical.sql', NULL);
INSERT INTO `en_updatelog` (`ul_key`, `ul_value`) VALUES('user_former_groups-ufg_group-patch-ufg_group-length-increase-255.sql', NULL);
INSERT INTO `en_updatelog` (`ul_key`, `ul_value`) VALUES('user_groups-ug_group-patch-ug_group-length-increase-255.sql', NULL);
INSERT INTO `en_updatelog` (`ul_key`, `ul_value`) VALUES('user_properties-up_property-patch-up_property.sql', NULL);

--
-- Dumping data for table `en_user`
--

INSERT INTO `en_user` (`user_id`, `user_name`, `user_real_name`, `user_password`, `user_newpassword`, `user_newpass_time`, `user_email`, `user_touched`, `user_token`, `user_email_authenticated`, `user_email_token`, `user_email_token_expires`, `user_registration`, `user_editcount`, `user_password_expires`) VALUES(1, 'Foss', 'foss', 0x3a70626b6466323a7368613235363a31303030303a3132383a54624132486c56423777397763774e6d37414d7651673d3d3a4549414a504e5a754e4246457248496f4f64366369657051583061663362547a4c692b6f5659377a69775a713078317237636471303045617856544872456c4e304e51386e67676154305a6a4c57434b3352336e656d79396166694b483732626f59764742426768506b4d76725a7378744f504276337753392f705773584d4f79417955586332714c312b41664531684561595a56784a6770637350485977566743724f627730473239383d, '', NULL, 'yomna.fahmy@espace.com.eg', '20151229211234', '71f079654959849c4406a4541c403f22', NULL, NULL, NULL, '20151229205007', 0, NULL);

--
-- Dumping data for table `en_user_groups`
--

INSERT INTO `en_user_groups` (`ug_user`, `ug_group`) VALUES(1, 'bureaucrat');
INSERT INTO `en_user_groups` (`ug_user`, `ug_group`) VALUES(1, 'sysop');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
