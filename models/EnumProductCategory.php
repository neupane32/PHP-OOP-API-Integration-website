<?php
enum ProductCategory: string {
    case ELECTRONICS = 'Electronics';
    case FURNITURE = 'Furniture';
    case CLOTHING = 'Clothing';
    case HOME = 'Home';
    case TOYS = 'Toys';
    case BOOKS = 'Books';

    public static function isValidCategory(string $category): bool {
        return in_array($category, array_column(self::cases(), 'value'));
    }
}
?>