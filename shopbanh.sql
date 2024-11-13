-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 13, 2024 lúc 04:39 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shopbanh`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category`
--

CREATE TABLE `category` (
  `CateID` int(11) NOT NULL,
  `CateName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `category`
--

INSERT INTO `category` (`CateID`, `CateName`) VALUES
(1, 'Cupcake'),
(2, 'Bentocake'),
(3, 'Cheesecake'),
(4, 'Layercake'),
(5, 'Mochicake'),
(6, 'Tiramisu'),
(7, 'Specialcake'),
(8, 'Whoopie');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order`
--

CREATE TABLE `order` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `NameOder` varchar(100) NOT NULL,
  `PhoneOder` varchar(50) NOT NULL,
  `AddressOder` varchar(100) NOT NULL,
  `Note` varchar(250) DEFAULT NULL,
  `Total` int(11) NOT NULL,
  `CreatTime` int(11) NOT NULL,
  `LastUpdate` int(11) NOT NULL,
  `Status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order`
--

INSERT INTO `order` (`OrderID`, `UserID`, `NameOder`, `PhoneOder`, `AddressOder`, `Note`, `Total`, `CreatTime`, `LastUpdate`, `Status`) VALUES
(32, 9, 'Tạ Văn Hải', '0328888888', 'ha noi', '', 250000, 1731463745, 2147483647, '0'),
(33, 8, 'Tạ Văn Hải', '0328888888', 'ha noi', '', 70000, 1731468338, 1731468338, '1');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orderdetails`
--

CREATE TABLE `orderdetails` (
  `DetailsID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `PriceP` decimal(15,2) NOT NULL,
  `CreatTime` int(11) NOT NULL,
  `LastUpdate` int(11) NOT NULL,
  `NoteCart` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orderdetails`
--

INSERT INTO `orderdetails` (`DetailsID`, `OrderID`, `ProductID`, `Quantity`, `PriceP`, `CreatTime`, `LastUpdate`, `NoteCart`) VALUES
(4, 32, 40, 1, 40000.00, 1731463745, 1731463745, 'Không có ghi chú'),
(5, 32, 46, 3, 60000.00, 1731463745, 1731463745, 'Không có ghi chú'),
(6, 33, 40, 1, 40000.00, 1731468338, 1731468338, 'Không có ghi chú');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product`
--

CREATE TABLE `product` (
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(50) NOT NULL,
  `Description` varchar(1000) NOT NULL,
  `CateID` int(11) NOT NULL,
  `ProductImage` varchar(255) NOT NULL,
  `Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product`
--

INSERT INTO `product` (`ProductID`, `ProductName`, `Description`, `CateID`, `ProductImage`, `Price`) VALUES
(39, 'Cupcake Chocolate', 'Cupcake vị chocolate đậm đà, hấp dẫn từ lớp cốt bánh mềm xốp đến lớp kem chocolate mịn màng. Hương vị chocolate thuần khiết giúp bạn tận hưởng vị ngọt béo của kem hòa quyện cùng cốt bánh, thích hợp cho các tín đồ yêu thích chocolate.', 1, './assets/img/products/Cupcake/2.jpg', 38000.00),
(40, 'Cupcake Matcha', 'Cupcake xanh mát được làm từ bột matcha Nhật Bản nguyên chất, tạo nên vị ngọt thanh và chút đắng nhẹ đặc trưng. Lớp kem matcha béo ngậy được trang trí phía trên giúp tăng hương vị và độ hấp dẫn, mang đến trải nghiệm mới mẻ.', 1, './assets/img/products/Cupcake/3.jpg', 40000.00),
(41, 'Cupcake Hoa Quả', 'Cupcake mềm xốp với lớp kem mát lạnh và trái cây tươi mọng trên mặt. Bánh kết hợp hương vị tự nhiên từ dâu tây, kiwi, và cam, đem lại cảm giác ngọt thanh và tươi mới, rất thích hợp cho những ai yêu thích hương vị trái cây tự nhiên.', 1, './assets/img/products/Cupcake/4.jpg', 37000.00),
(42, 'Bentocake Tình Yêu', 'Bentocake được trang trí tỉ mỉ với hình trái tim cùng những lời chúc ý nghĩa, là món quà tuyệt vời cho những dịp kỷ niệm hoặc để bày tỏ tình cảm. Bánh mềm mịn với lớp kem ngọt ngào, kết hợp màu sắc trang nhã, dễ thương.', 2, './assets/img/products/Bentocake/1.jpg', 50000.00),
(43, 'Bentocake Sô Cô La', 'Bentocake phủ lớp chocolate mịn màng và trang trí tinh tế, tạo nên hương vị đậm đà cho những ai yêu thích chocolate. Bánh có kích thước nhỏ gọn nhưng đầy đặn, thích hợp để thưởng thức hoặc tặng người thân.', 2, './assets/img/products/Bentocake/2.jpg', 52000.00),
(44, 'Bentocake Phong Cách Nhật', 'Với thiết kế nhỏ gọn, Bentocake phong cách Nhật Bản mang lại cảm giác nhẹ nhàng, thanh lịch. Bánh có lớp kem mềm, vị ngọt nhẹ, phù hợp với các dịp đặc biệt như sinh nhật hoặc ngày lễ, thể hiện sự tinh tế và chu đáo.', 2, './assets/img/products/Bentocake/3.jpg', 55000.00),
(45, 'Bentocake Thiên Nhiên', 'Bánh Bentocake được làm từ các nguyên liệu tự nhiên, với lớp kem mát dịu và hương vị trái cây. Phù hợp cho những ai ưa chuộng hương vị thuần khiết, không quá ngọt và có cảm giác tươi mới trong từng miếng cắn.', 2, './assets/img/products/Bentocake/4.jpg', 53000.00),
(46, 'Cheesecake Việt Quất', 'Cheesecake mịn màng với lớp việt quất chua ngọt phía trên, giúp cân bằng vị béo ngậy của kem phô mai. Bánh có phần đế giòn, thơm, khi kết hợp cùng lớp kem phô mai tạo nên một hương vị độc đáo, đầy hấp dẫn.', 3, './assets/img/products/Cheesecake/1.jpg', 60000.00),
(47, 'Cheesecake Chanh Dây', 'Cheesecake thơm mát với lớp chanh dây tươi trên mặt, mang lại vị chua thanh mát kết hợp cùng kem phô mai béo ngậy. Mỗi miếng bánh là sự hài hòa giữa độ ngọt và chua, giúp cân bằng vị giác và mang đến cảm giác sảng khoái.', 3, './assets/img/products/Cheesecake/2.jpg', 62000.00),
(48, 'Cheesecake Trà Xanh', 'Cheesecake vị trà xanh Nhật Bản với lớp kem mịn màng và phần đế giòn. Hương trà xanh đậm đà giúp tạo nên một món bánh thơm ngon, không quá ngọt, phù hợp với những ai yêu thích hương vị thanh khiết và đậm chất Á Đông.', 3, './assets/img/products/Cheesecake/3.jpg', 61000.00),
(49, 'Cheesecake Dâu Tằm', 'Lớp dâu tằm ngọt chua được phủ trên bề mặt bánh phô mai mịn màng, tạo nên sự hấp dẫn đặc biệt cho món bánh cheesecake dâu tằm này. Vị dâu tằm tươi mát giúp cân bằng độ béo của kem phô mai, tạo nên hương vị hài hòa.', 3, './assets/img/products/Cheesecake/4.jpg', 63000.00),
(50, 'Layercake Hồng Hạnh Phúc', 'Layercake nhiều lớp màu hồng dịu, mang đến sự hạnh phúc và lãng mạn cho những dịp đặc biệt. Bánh được trang trí nhẹ nhàng, cốt bánh mềm và lớp kem mịn màng tạo nên cảm giác thư giãn trong từng miếng cắn.', 4, './assets/img/products/Layercake/1.jpg', 75000.00),
(51, 'Layercake Sô Cô La', 'Layercake nhiều lớp với hương vị chocolate đậm đà, thích hợp cho những ai yêu thích vị ngọt ngào pha chút đắng của chocolate. Bánh có lớp kem dày và mềm mịn, mang đến trải nghiệm vị giác hoàn hảo.', 4, './assets/img/products/Layercake/2.jpg', 77000.00),
(52, 'Layercake Vanilla', 'Bánh layercake với hương vanilla ngọt ngào, lớp kem mềm, phù hợp cho các bữa tiệc và dịp đặc biệt. Mỗi lớp bánh là sự hòa quyện giữa vị ngọt của kem và vị thanh nhẹ của vanilla.', 4, './assets/img/products/Layercake/3.jpg', 76000.00),
(53, 'Layercake Kem Phô Mai', 'Layercake được làm từ nhiều lớp bánh xen kẽ với lớp kem phô mai béo ngậy. Hương vị nhẹ nhàng, ngọt ngào của kem phô mai kết hợp với cốt bánh mềm, tạo cảm giác thú vị và ngon miệng.', 4, './assets/img/products/Layercake/4.jpg', 78000.00),
(54, 'Mochicake Trà Xanh', 'Mochicake nhân trà xanh, có độ dai nhẹ và vị ngọt thanh. Lớp bột mochi mềm mịn bao quanh phần nhân trà xanh Nhật Bản, tạo nên một món bánh truyền thống độc đáo và tinh tế.', 5, './assets/img/products/Mochicake/1.jpg', 45000.00),
(55, 'Mochicake Đậu Đỏ', 'Mochicake với nhân đậu đỏ ngọt bùi, tạo nên sự cân bằng hoàn hảo giữa lớp bột mochi dai và phần nhân thơm ngon bên trong. Món bánh này mang đến hương vị nhẹ nhàng, đậm chất Á Đông.', 5, './assets/img/products/Mochicake/2.jpg', 46000.00),
(56, 'Mochicake Chocolate', 'Mochicake chocolate đậm đà với lớp nhân chocolate mềm tan trong miệng. Món bánh này là sự kết hợp hoàn hảo giữa hương vị truyền thống và sự hiện đại, mang lại cảm giác thú vị khi thưởng thức.', 5, './assets/img/products/Mochicake/3.jpg', 47000.00),
(57, 'Mochicake Dừa Tươi', 'Mochicake với lớp nhân dừa tươi thơm ngon, lớp bột mochi bên ngoài mềm dẻo tạo cảm giác lạ miệng. Hương vị dừa tự nhiên, ngọt thanh làm tăng thêm sức hấp dẫn cho món bánh.', 5, './assets/img/products/Mochicake/4.jpg', 48000.00),
(58, 'Tiramisu Cà Phê', 'Tiramisu vị cà phê truyền thống với lớp kem mịn và hương vị đặc trưng từ espresso. Phù hợp với những ai yêu thích sự kết hợp giữa vị ngọt béo của kem và vị đắng nhẹ của cà phê.', 6, './assets/img/products/Tiramisu/1.jpg', 70000.00),
(59, 'Tiramisu Chocolate', 'Tiramisu chocolate với lớp kem béo ngậy, hương chocolate đậm đà, tan chảy trong miệng, tạo nên một món bánh hoàn hảo cho những người yêu thích hương vị ngọt ngào.', 6, './assets/img/products/Tiramisu/2.jpg', 71000.00),
(60, 'Tiramisu Trà Xanh', 'Tiramisu kết hợp với hương trà xanh Nhật Bản, tạo nên món bánh mới mẻ và hấp dẫn. Lớp kem trà xanh mịn màng hòa quyện cùng bánh ladyfinger mềm, mang lại trải nghiệm vị giác thú vị.', 6, './assets/img/products/Tiramisu/3.jpg', 72000.00),
(61, 'Tiramisu Dâu Tây', 'Tiramisu dâu tây với vị ngọt tự nhiên và chút chua nhẹ từ dâu tây tươi, kết hợp cùng lớp kem béo ngậy, là món bánh hoàn hảo cho các dịp đặc biệt.', 6, './assets/img/products/Tiramisu/4.jpg', 73000.00),
(62, 'Specialcake Sô Cô La Hạnh Nhân', 'Specialcake kết hợp chocolate và hạnh nhân giòn tan, mang đến hương vị đậm đà, ngọt ngào và lôi cuốn. Bánh thích hợp cho các buổi tiệc sang trọng.', 7, './assets/img/products/Specialcake/1.jpg', 90000.00),
(63, 'Specialcake Tạo Hình Hoa', 'Specialcake với thiết kế tạo hình hoa tinh tế, phù hợp cho các dịp lễ lớn. Bánh có lớp kem mịn và vị ngọt nhẹ, tạo cảm giác sang trọng và tinh tế.', 7, './assets/img/products/Specialcake/2.jpg', 95000.00),
(64, 'Specialcake Kem Bơ', 'Specialcake với lớp kem bơ dày, béo ngậy, tan chảy trong miệng. Hương vị bơ thơm ngọt hài hòa với cốt bánh mềm xốp, mang đến sự hài lòng tuyệt đối.', 7, './assets/img/products/Specialcake/3.jpg', 92000.00),
(65, 'Specialcake Vani Hồng Phấn', 'Specialcake với hương vanilla thanh nhã, lớp kem màu hồng phấn tươi sáng, là lựa chọn lý tưởng cho các dịp đặc biệt cần sự tinh tế.', 7, './assets/img/products/Specialcake/4.jpg', 94000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Fullname` varchar(50) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Created_time` datetime NOT NULL,
  `UserRole` varchar(20) NOT NULL,
  `Phone` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`UserID`, `Fullname`, `Username`, `Password`, `Created_time`, `UserRole`, `Phone`, `Email`) VALUES
(8, 'Văn Hải 2', 'vanhai2', '$2y$10$mhVHHWh694IIsU8DvLg/E.VZW0Qb5I8PaaLRo1dRlNNPrFixbw3VG', '2024-11-12 18:44:03', '1', 2147483647, 'vinhlt@ltsgroup.tech'),
(9, 'Văn Hải', 'vanhai', '$2y$10$NfVK5Fvi0Bl6D/M11tMSoOJl7yOML0vvSR088vE49hyr.L703GwTy', '2024-11-12 18:46:30', '2', 357256358, 'haivansteam@gmail.com');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`CateID`);

--
-- Chỉ mục cho bảng `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `order_ibfk_1` (`UserID`);

--
-- Chỉ mục cho bảng `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`DetailsID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `orderdetails_ibfk_2` (`ProductID`);

--
-- Chỉ mục cho bảng `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `CateID` (`CateID`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `category`
--
ALTER TABLE `category`
  MODIFY `CateID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `order`
--
ALTER TABLE `order`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `DetailsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `product`
--
ALTER TABLE `product`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Các ràng buộc cho bảng `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`);

--
-- Các ràng buộc cho bảng `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`CateID`) REFERENCES `category` (`CateID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
