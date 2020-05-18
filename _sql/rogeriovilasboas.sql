-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 23-Fev-2018 às 01:04
-- Versão do servidor: 10.1.16-MariaDB
-- PHP Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rogeriovilasboas`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `banner`
--

DROP TABLE IF EXISTS `banner`;
CREATE TABLE `banner` (
  `idBanner` int(11) NOT NULL,
  `titulo` varchar(128) CHARACTER SET latin1 NOT NULL,
  `url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `descricao` text CHARACTER SET latin1,
  `link` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `mostrarTodos` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `novaJanela` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `altura` int(3) DEFAULT NULL,
  `largura` int(3) DEFAULT NULL,
  `visualizacao` int(11) DEFAULT NULL,
  `clique` int(11) DEFAULT NULL,
  `dataIni` date NOT NULL,
  `dataFim` date DEFAULT NULL,
  `dataRegistro` datetime NOT NULL,
  `ativo` int(1) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `banner`
--

INSERT INTO `banner` (`idBanner`, `titulo`, `url`, `descricao`, `link`, `mostrarTodos`, `novaJanela`, `altura`, `largura`, `visualizacao`, `clique`, `dataIni`, `dataFim`, `dataRegistro`, `ativo`) VALUES
(1, 'Cirurgia', '1-20170531190119-670867.jpg', 'O implante de prótese de silicone nas mamas é uma das cirurgias mais procuradas', 'Protese de mama', 0, 0, NULL, NULL, NULL, NULL, '2017-05-31', NULL, '2017-05-31 19:01:19', 1),
(2, 'Dicas', '2-20170601050053-113922.jpg', 'No dia da cirurgia, o paciente deve se programar para chegar com antecedência a clínica.', NULL, 0, 0, NULL, NULL, NULL, NULL, '2017-05-31', NULL, '2017-06-01 04:57:47', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `bannermenu`
--

DROP TABLE IF EXISTS `bannermenu`;
CREATE TABLE `bannermenu` (
  `idMenu` int(11) NOT NULL,
  `idBanner` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `bannermenu`
--

INSERT INTO `bannermenu` (`idMenu`, `idBanner`) VALUES
(0, 1),
(0, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `conteudo`
--

DROP TABLE IF EXISTS `conteudo`;
CREATE TABLE `conteudo` (
  `idConteudo` int(10) UNSIGNED NOT NULL,
  `idConteudoRelacionado` int(10) DEFAULT '0',
  `titulo` varchar(255) NOT NULL,
  `resumo` text NOT NULL,
  `descricao` text NOT NULL,
  `codigo` int(3) DEFAULT NULL,
  `fonte` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `imagem` varchar(100) DEFAULT NULL,
  `mostrarData` int(1) UNSIGNED NOT NULL DEFAULT '1',
  `destaque` int(1) UNSIGNED NOT NULL,
  `dataRegistro` datetime DEFAULT NULL,
  `dataIni` datetime NOT NULL,
  `dataFim` datetime DEFAULT NULL,
  `ativo` int(1) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `conteudo`
--

INSERT INTO `conteudo` (`idConteudo`, `idConteudoRelacionado`, `titulo`, `resumo`, `descricao`, `codigo`, `fonte`, `url`, `imagem`, `mostrarData`, `destaque`, `dataRegistro`, `dataIni`, `dataFim`, `ativo`) VALUES
(1, 0, 'Cirurgia não é mágica', 'Cirurgia não é mágica. Por isso, desconfie de facilidades como cirurgias milagrosas ou preços incoerentes com os procedimentos. Desconfie de bargan has ou de ofertas muito baratas.', 'Cirurgia n&atilde;o &eacute; m&aacute;gica. Por isso, desconfie de facilidades como cirurgias milagrosas ou pre&ccedil;os incoerentes com os procedimentos. Desconfie de bargan has ou de ofertas muito baratas.', NULL, NULL, '', NULL, 0, 0, '2017-06-01 17:20:23', '2017-06-01 17:22:29', NULL, 1),
(2, 0, 'O período de recuperação é muito importante', 'O período de recuperação é muito importante, por isso obedeça todas orientações dadas por seu cirurgião.', '<p>O per&iacute;odo de recupera&ccedil;&atilde;o &eacute; muito importante, por isso obede&ccedil;a todas orienta&ccedil;&otilde;es dadas por seu cirurgi&atilde;o.</p>', NULL, NULL, '', NULL, 0, 0, '2017-06-01 17:20:50', '2017-06-01 17:21:59', NULL, 1),
(3, 0, 'No dia da cirurgia', 'No dia da cirurgia, o paciente deve se programar para chegar com antecedência a clínica e levar roupas largas e confortáveis para vestir quando tiver alta, e também óculos escuros, no caso de cirurgias das pálpebras.', '<p>No dia da cirurgia, o paciente deve se programar para chegar com anteced&ecirc;ncia a cl&iacute;nica e levar roupas largas e confort&aacute;veis para vestir quando tiver alta, e tamb&eacute;m &oacute;culos escuros, no caso de cirurgias das p&aacute;lpebras.</p>', NULL, NULL, '', NULL, 1, 0, '2017-06-01 17:21:42', '2017-06-01 17:21:42', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `conteudomenu`
--

DROP TABLE IF EXISTS `conteudomenu`;
CREATE TABLE `conteudomenu` (
  `idConteudo` int(10) UNSIGNED NOT NULL,
  `idMenu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `conteudomenu`
--

INSERT INTO `conteudomenu` (`idConteudo`, `idMenu`) VALUES
(1, 3),
(2, 3),
(3, 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `menu`
--

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `idMenu` int(11) NOT NULL,
  `idMenuRelacionado` int(11) NOT NULL DEFAULT '0',
  `descricao` varchar(128) CHARACTER SET latin1 NOT NULL,
  `url` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `ordem` int(11) NOT NULL,
  `novaJanela` int(1) UNSIGNED NOT NULL DEFAULT '0',
  `dataRegistro` datetime NOT NULL,
  `restrito` tinyint(1) NOT NULL DEFAULT '0',
  `ativo` int(1) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `menu`
--

INSERT INTO `menu` (`idMenu`, `idMenuRelacionado`, `descricao`, `url`, `ordem`, `novaJanela`, `dataRegistro`, `restrito`, `ativo`) VALUES
(1, 0, 'O médico', 'omedico', 1, 0, '2017-05-19 00:00:00', 0, 1),
(2, 0, 'Cirurgia', 'cirurgia', 2, 0, '2017-05-19 00:00:00', 0, 1),
(3, 0, 'Dicas', 'dicas', 3, 0, '2017-05-19 00:00:00', 0, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `modulo`
--

DROP TABLE IF EXISTS `modulo`;
CREATE TABLE `modulo` (
  `idModulo` int(11) NOT NULL,
  `descricao` varchar(255) CHARACTER SET latin1 NOT NULL,
  `url` varchar(100) CHARACTER SET latin1 NOT NULL,
  `ativo` smallint(1) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `modulo`
--

INSERT INTO `modulo` (`idModulo`, `descricao`, `url`, `ativo`) VALUES
(1, 'Conteúdo', 'conteudo', 1),
(2, 'Contato', 'contato', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `modulomenu`
--

DROP TABLE IF EXISTS `modulomenu`;
CREATE TABLE `modulomenu` (
  `idModulo` int(11) NOT NULL,
  `idMenu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `modulomenu`
--

INSERT INTO `modulomenu` (`idModulo`, `idMenu`) VALUES
(1, 1),
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
  `dataRegistro` datetime NOT NULL,
  `nome` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `senha` varchar(32) NOT NULL,
  `primeiroAcesso` smallint(1) UNSIGNED NOT NULL DEFAULT '0',
  `ativo` smallint(1) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `dataRegistro`, `nome`, `email`, `senha`, `primeiroAcesso`, `ativo`) VALUES
(1, '2016-04-28 00:00:00', 'Eduardo', 'eduardo@eblue.com.br', 'c0dc1051164bf1848c76c1e9fce2544d', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`idBanner`);

--
-- Indexes for table `bannermenu`
--
ALTER TABLE `bannermenu`
  ADD PRIMARY KEY (`idMenu`,`idBanner`);

--
-- Indexes for table `conteudo`
--
ALTER TABLE `conteudo`
  ADD PRIMARY KEY (`idConteudo`);

--
-- Indexes for table `conteudomenu`
--
ALTER TABLE `conteudomenu`
  ADD PRIMARY KEY (`idConteudo`,`idMenu`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`idMenu`);

--
-- Indexes for table `modulo`
--
ALTER TABLE `modulo`
  ADD PRIMARY KEY (`idModulo`);

--
-- Indexes for table `modulomenu`
--
ALTER TABLE `modulomenu`
  ADD PRIMARY KEY (`idModulo`,`idMenu`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `idBanner` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `conteudo`
--
ALTER TABLE `conteudo`
  MODIFY `idConteudo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `idMenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `modulo`
--
ALTER TABLE `modulo`
  MODIFY `idModulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
