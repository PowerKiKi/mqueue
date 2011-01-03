-- Make email unique to prevent duplicate registration
ALTER TABLE `user` ADD UNIQUE (`email`); 
