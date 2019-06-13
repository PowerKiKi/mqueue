-- Support ID with any digits
ALTER TABLE `status`
    DROP FOREIGN KEY `status_ibfk_2` ;
ALTER TABLE `status` CHANGE `idMovie` `idMovie` int(11) NOT NULL;
ALTER TABLE `movie` CHANGE `id` `id` int(11) NOT NULL;
ALTER TABLE `status`
    ADD CONSTRAINT `status_ibfk_2` FOREIGN KEY (`idMovie`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
