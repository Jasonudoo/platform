DROP TRIGGER IF EXISTS member_register_datetime;
DROP TRIGGER IF EXISTS member_visit_datetime;
DROP TRIGGER IF EXISTS prod_created_datetime;
DROP TRIGGER IF EXISTS prod_updated_datetime;
DROP TRIGGER IF EXISTS cg_created_datetime;
DROP TRIGGER IF EXISTS cg_updated_datetime;
DROP TRIGGER IF EXISTS pk_created_datetime;
DROP TRIGGER IF EXISTS pk_updated_datetime;
DROP TRIGGER IF EXISTS order_created_datetime;
DROP TRIGGER IF EXISTS order_modified_datetime;
DROP TRIGGER IF EXISTS cart_created_datetime;
DROP TRIGGER IF EXISTS cart_updated_datetime;
DROP TRIGGER IF EXISTS img_created_datetime;
DROP TRIGGER IF EXISTS img_updated_datetime;

DELIMITER //
CREATE TRIGGER member_register_datetime BEFORE INSERT ON `tbl_schorder_member`
   FOR EACH ROW
   BEGIN
	  if new.register_date is null then
	     set new.register_date = now();
	  end if;
   END;//

DELIMITER //
CREATE TRIGGER member_visit_datetime BEFORE UPDATE ON `tbl_schorder_member`
   FOR EACH ROW
   BEGIN
	  if new.lastvisit_date is null then
	     set new.lastvisit_date = now();
	  end if;
   END;//

DELIMITER //
CREATE TRIGGER prod_created_datetime BEFORE INSERT ON `tbl_schorder_product`
   FOR EACH ROW 
   BEGIN
      if new.created_on is null then
         set new.created_on = now();
      end if;
   END;//

DELIMITER //
CREATE TRIGGER prod_updated_datetime BEFORE UPDATE ON `tbl_schorder_product`
   FOR EACH ROW 
   BEGIN
      if new.modified_on is null then
         set new.modified_on = now();
      end if;
   END;//

DELIMITER //
CREATE TRIGGER pk_created_datetime BEFORE INSERT ON `tbl_schorder_package`
   FOR EACH ROW 
   BEGIN
      if new.created_on is null then
         set new.created_on = now();
      end if;
   END;//

DELIMITER //
CREATE TRIGGER pk_updated_datetime BEFORE UPDATE ON `tbl_schorder_package`
   FOR EACH ROW 
   BEGIN
      if new.modified_on is null then
         set new.modified_on = now();
      end if;
   END;//

DELIMITER //
CREATE TRIGGER order_created_datetime BEFORE INSERT ON `tbl_schorder_order`
   FOR EACH ROW
   BEGIN
	  if new.created_on is null then
	     set new.created_on = now();
	  end if;
   END;//

DELIMITER //
CREATE TRIGGER order_modified_datetime BEFORE UPDATE ON `tbl_schorder_order`
   FOR EACH ROW
   BEGIN
	  if new.modified_on is null then
	     set new.modified_on = now();
	  end if;
   END;//

DELIMITER //
CREATE TRIGGER cart_created_datetime BEFORE INSERT ON `tbl_schorder_cart`
   FOR EACH ROW 
   BEGIN
      if new.created_on is null then
         set new.created_on = now();
      end if;
   END;//


DELIMITER //
CREATE TRIGGER cart_updated_datetime BEFORE UPDATE ON `tbl_schorder_cart`
   FOR EACH ROW 
   BEGIN
      if new.modified_on is null then
         set new.modified_on = now();
      end if;
   END;//

DELIMITER //
CREATE TRIGGER img_created_datetime BEFORE INSERT ON `tbl_schorder_product_image`
   FOR EACH ROW 
   BEGIN
      if new.created_on is null then
         set new.created_on = now();
      end if;
   END;//

DELIMITER //
CREATE TRIGGER img_updated_datetime BEFORE UPDATE ON `tbl_schorder_product_image`
   FOR EACH ROW 
   BEGIN
      if new.modified_on is null then
         set new.modified_on = now();
      end if;
   END;//

INSERT INTO `tbl_schorder_package` (`pid`, `name`, `description`, `ordering`, `created_by`, `published`)
VALUES('100001', 'Vegetables', 'Vegetables Category', '1', '1', '0'),
('100002', 'Fruit', 'Fruit Category', '2', '1', '0'),
('100003', 'Milk', 'Milk Category', '3', '1', '0');

delete from tbl_assets where name='com_scheduleorder';
delete from tbl_extensions where element='com_scheduleorder';
delete from tbl_menu where link = 'index.php?option=com_scheduleorder';