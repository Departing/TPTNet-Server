SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `SandboxToy_`
--

-- --------------------------------------------------------

--
-- Table structure for table `Comments`
--

CREATE TABLE `Comments` (
  `ID` int(255) NOT NULL COMMENT 'The index ID',
  `Author` int(255) NOT NULL COMMENT 'The author''s user ID',
  `Comment` varchar(2048) NOT NULL COMMENT 'Comment content',
  `Date` int(255) NOT NULL COMMENT 'Date/time in epoch format',
  `Save_ID` int(255) NOT NULL COMMENT 'Save ID where the comment is located',
  `IP_Address` varbinary(16) NOT NULL COMMENT 'The User''s IP in an encoded format'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Favourites`
--

CREATE TABLE `Favourites` (
  `ID` int(255) NOT NULL COMMENT 'Index ID',
  `User` int(255) NOT NULL COMMENT 'The User ID of who favourited the save',
  `Save_ID` int(255) NOT NULL COMMENT 'Save ID that was favourited'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Registered`
--

CREATE TABLE `Registered` (
  `ID` int(255) NOT NULL COMMENT 'User ID (Index)',
  `ref_ID` decimal(22,5) NOT NULL COMMENT 'Reference ID (Allows for future flexibility)',
  `Email` varchar(655) NOT NULL COMMENT 'Email in plain text',
  `Username` varchar(655) NOT NULL COMMENT 'Username in plain text',
  `Hash` varchar(655) NOT NULL COMMENT 'Hash of the TPT hash sent to the server (will rehash in the future)',
  `Date` int(255) NOT NULL COMMENT 'Date of registration in epoch format',
  `Elevation` int(3) NOT NULL DEFAULT '0' COMMENT 'Their role status.',
  `Status` int(255) NOT NULL DEFAULT '1' COMMENT 'If they are banned, unregistered, or suspended.'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Saves`
--

CREATE TABLE `Saves` (
  `ID` int(11) NOT NULL COMMENT 'Save ID (Index)',
  `Author` int(255) NOT NULL COMMENT 'The author''s user ID',
  `Name` varchar(655) NOT NULL COMMENT 'The save name',
  `Description` varchar(655) NOT NULL COMMENT 'The description of the save',
  `Status` int(3) NOT NULL DEFAULT '0' COMMENT 'Status which defines if it''s active or deleted',
  `Published` int(3) NOT NULL DEFAULT '0' COMMENT 'Published or un-published ("hidden")',
  `Date_Uploaded` int(255) NOT NULL COMMENT 'Date of creation in epoch format',
  `Date_Updated` int(255) NOT NULL COMMENT 'Date of save edit in epoch format',
  `Votes` int(255) NOT NULL DEFAULT '0' COMMENT 'Total number of votes (obselete in some cases)',
  `Views` int(255) NOT NULL DEFAULT '0' COMMENT 'Total number of views (again, obselete in some cases)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Saves__History`
--

CREATE TABLE `Saves__History` (
  `ID` int(11) NOT NULL COMMENT 'Index ID',
  `Save_ID` int(255) NOT NULL COMMENT 'Save ID of what the edit is from',
  `Author` int(255) NOT NULL COMMENT 'Author user ID',
  `Name` varchar(655) NOT NULL COMMENT 'Name of save (future uses possible)',
  `Description` varchar(655) NOT NULL COMMENT 'Description of save',
  `Date_Updated` int(255) NOT NULL COMMENT 'Epoch time of date when save was edited'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Sessions`
--

CREATE TABLE `Sessions` (
  `ID` int(255) NOT NULL COMMENT 'ID of session',
  `User` int(255) NOT NULL COMMENT 'User ID of who the session belongs to',
  `SessionID` varchar(655) NOT NULL COMMENT 'SessionID for TPT',
  `SessionKey` varchar(10) NOT NULL COMMENT 'Session Key for TPT',
  `Date` int(255) NOT NULL COMMENT 'Date of creation in epoch format',
  `Status` int(3) NOT NULL DEFAULT '1' COMMENT 'Determines if the session is active or revoked.',
  `IP_Address` varbinary(16) NOT NULL COMMENT 'IP address of user who logged in (encoded)',
  `Type` int(255) NOT NULL DEFAULT '0' COMMENT 'Session type (TPTNet use only, web)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Tags`
--

CREATE TABLE `Tags` (
  `ID` int(255) NOT NULL COMMENT 'ID of tag (Index)',
  `Save_ID` int(255) NOT NULL COMMENT 'Save ID where the tag is located',
  `Tag` varchar(655) NOT NULL COMMENT 'Actual tag (the one word)',
  `Author` int(255) NOT NULL COMMENT 'Author user ID',
  `Date` int(255) NOT NULL COMMENT 'Date of tag creation in epoch format',
  `Status` int(255) NOT NULL DEFAULT '0' COMMENT 'Status of tag (published 0/deleted 1)',
  `IP_Address` varbinary(16) NOT NULL COMMENT 'IP address of user who tagged it (encoded)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Views`
--

CREATE TABLE `Views` (
  `ID` int(255) NOT NULL COMMENT 'Index ID of view',
  `Save_ID` int(255) NOT NULL COMMENT 'Save ID of where the view counted',
  `User` int(255) NOT NULL COMMENT 'User ID of who viewed the save',
  `Date` int(255) NOT NULL COMMENT 'When did they view it (epoch format)',
  `IP_Address` varbinary(16) NOT NULL COMMENT 'IP address (encoded)',
  `Type` int(255) NOT NULL DEFAULT '0' COMMENT 'Type of view (future use in web vs client views)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Votes`
--

CREATE TABLE `Votes` (
  `ID` int(255) NOT NULL COMMENT 'ID of the vote (index)',
  `Save_ID` int(255) NOT NULL COMMENT 'Where the voted was counted (Save ID)',
  `User` int(255) NOT NULL COMMENT 'Person who voted for the save (User ID)',
  `Date` int(255) NOT NULL COMMENT 'Date of their vote being casted in epoch format',
  `IP_Address` varbinary(16) NOT NULL COMMENT 'IP address of voter (prevent vote fraud) (Encoded)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Comments`
--
ALTER TABLE `Comments`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Favourites`
--
ALTER TABLE `Favourites`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Registered`
--
ALTER TABLE `Registered`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Saves`
--
ALTER TABLE `Saves`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Saves__History`
--
ALTER TABLE `Saves__History`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Sessions`
--
ALTER TABLE `Sessions`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Tags`
--
ALTER TABLE `Tags`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Views`
--
ALTER TABLE `Views`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Votes`
--
ALTER TABLE `Votes`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Comments`
--
ALTER TABLE `Comments`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT COMMENT 'The index ID';
--
-- AUTO_INCREMENT for table `Favourites`
--
ALTER TABLE `Favourites`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT COMMENT 'Index ID';
--
-- AUTO_INCREMENT for table `Registered`
--
ALTER TABLE `Registered`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT COMMENT 'User ID (Index)';
--
-- AUTO_INCREMENT for table `Saves`
--
ALTER TABLE `Saves`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Save ID (Index)';
--
-- AUTO_INCREMENT for table `Saves__History`
--
ALTER TABLE `Saves__History`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Index ID';
--
-- AUTO_INCREMENT for table `Sessions`
--
ALTER TABLE `Sessions`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT COMMENT 'ID of session';
--
-- AUTO_INCREMENT for table `Tags`
--
ALTER TABLE `Tags`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT COMMENT 'ID of tag (Index)';
--
-- AUTO_INCREMENT for table `Views`
--
ALTER TABLE `Views`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT COMMENT 'Index ID of view';
--
-- AUTO_INCREMENT for table `Votes`
--
ALTER TABLE `Votes`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT COMMENT 'ID of the vote (index)';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
