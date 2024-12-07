<?php

namespace Database\Seeders;

use App\Models\ProductParam;
use App\Models\ProductParamItem;
use Illuminate\Database\Seeder;

class ProductParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create common product parameters
        $this->createParameter('Brand', 'select', true, [
            'BOSCH', 'DeWALT', 'Makita', 'Milwaukee', 'Hilti', 'Festool',
            'Metabo', 'Ryobi', 'Stanley', 'Craftsman'
        ]);

        $this->createParameter('Color', 'color', true, [
            '#000000', // Black
            '#FFFF00', // Yellow
            '#0000FF', // Blue
            '#FF0000', // Red
            '#008000', // Green
            '#FFA500', // Orange
            '#808080', // Gray
            '#FFD700', // Gold
            '#FF69B4', // Hot Pink
            '#A52A2A'  // Brown
        ]);

        $this->createParameter('Power Type', 'select', true, [
            'Cordless', 'Corded Electric', 'Pneumatic', 'Hydraulic', 'Manual'
        ]);

        $this->createParameter('Voltage', 'select', true, [
            '12V', '18V', '20V', '24V', '36V', '40V', '60V'
        ]);

        $this->createParameter('Weight (kg)', 'number', true);
        
        $this->createParameter('Power Output (W)', 'number', true);
        
        $this->createParameter('Battery Included', 'boolean', true);
        
        $this->createParameter('Warranty (Years)', 'number', true);

        $this->createParameter('Features', 'multiselect', true, [
            'Brushless Motor', 'LED Light', 'Variable Speed', 'Electronic Brake',
            'Dust Collection', 'Quick Change Chuck', 'Anti-Vibration',
            'Overload Protection', 'Battery Gauge', 'Soft Start'
        ]);

        $this->createParameter('Package Contents', 'text', false);

        // Create some random parameters for variety
        for ($i = 0; $i < 5; $i++) {
            $param = ProductParam::factory()->create();
            ProductParamItem::factory()
                ->count(rand(3, 8))
                ->create(['product_param_id' => $param->id]);
        }
    }

    /**
     * Create a parameter with its items
     */
    private function createParameter(string $name, string $type, bool $filterable, array $values = []): void
    {
        $param = ProductParam::create([
            'name' => $name,
            'type' => $type,
            'allow_filtering' => $filterable
        ]);

        if (empty($values)) {
            // For parameters without predefined values, create random items
            ProductParamItem::factory()
                ->count(rand(3, 8))
                ->create(['product_param_id' => $param->id]);
            return;
        }

        // Create items for predefined values
        foreach ($values as $value) {
            ProductParamItem::create([
                'product_param_id' => $param->id,
                'title' => $value,
                'value' => $value
            ]);
        }
    }
}
