<?php
enum ProductCategory: string {
    case LAPTOPS = 'Laptops';
    case SMARTPHONES = 'Smartphones';
    case ACCESSORIES = 'Accessories';
    case SOFTWARE = 'Software';
    case PERIPHERALS = 'Peripherals';
    case GAMING = 'Gaming';
    case NETWORKING = 'Networking';

    public static function isValidCategory(string $category): bool {
        return in_array($category, array_column(self::cases(), 'value'));
    }
}
?>