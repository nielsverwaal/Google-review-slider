<?php

/**
 * ACF Block declaration
 *
 * @package WordPress
 * @subpackage WP_Lemon
 */

namespace WP_Lemon\Child\Blocks;

use HighGround\Bulldozer\BlockRendererV2 as BlockRenderer;
use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * Menukaart block that can be copied for making extra blocks.
 *
 * Follow the API standard of https://www.advancedcustomfields.com/resources/acf-blocks-with-block-json/
 */
class Menukaart_Block extends BlockRenderer
{
    const NAME = 'menukaart';

	/**
	 * Extend the base context of our block.
	 * With this function we can add for example a query or
	 * other custom content.
	 *
	 * @param array $context      Holds the block data.
	 * @return array  $context    Returns the array with the extra content that merges into the original block context.
	 */
	public function block_context($context): array
	{
		$args = [
			// 'InnerBlocks' => self::create_inner_blocks(['core/heading', 'core/paragraph']),
		];

		return array_merge($context, $args);
	}

    /**
	 * Register fields to the block.
	 *
	 * @link https://github.com/StoutLogic/acf-builder
	 * @return FieldsBuilder
	 */
	public function add_fields(): FieldsBuilder
	{
		$this->registered_fields
            ->addRepeater('menukaarten', [
                'label' => 'Menukaarten',
                'min' => 1,
                'layout' => 'block',
                ])
            ->addText('titel', [
                    'label' => 'Titel van de kaart',
                    'required' => true,
            ])
                ->addRepeater('menukaart_sectie', [
                    'label' => 'Menukaart sectie',
                    'instructions' => 'Bijvoorbeeld: Voorgerechten, Hoofdgerechten, Desserts',
                    'min' => 0,
                    'layout' => 'block',
                    ])
                ->addText('titel', [
                    'label' => 'Titel van de sectie',
                    'required' => false,
                ])
                    ->addRepeater('artikel', [
                        'label' => 'Artikel',
                        'instructions' => 'Voer artikelen in zoals dranken, gerechten, etc.',
                        'min' => 0,
                        'layout' => 'block',           
                        ])
                        ->addText('title', ['label' => 'Titel gerecht'])
                        ->addText('price', ['label' => 'Prijs'])
                        ->addTextarea('description', ['label' => 'Omschrijving'])
                        ->addCheckbox('allergens', [
                            'label' => 'Allergenen',
                            'choices' => [
                            'gluten' => 'Gluten',
                            'ei' => 'Ei',
                            'melk' => 'Melk',
                            'lactose' => 'Lactose',
                            'selderij' => 'Selderij',
                            'vis' => 'Vis',
                            'lupine' => 'Lupine',
                            'schaaldieren' => 'Schaaldieren',
                            'mosterd' => 'Mosterd',
                            'noten' => 'Noten',
                            'pinda' => 'Pinda',
                            'sesam' => 'Sesam',
                            'weekdieren' => 'Weekdieren',
                            'soja' => 'Soja',
                            'sulfiet' => 'Sulfiet',
                            ],
                        'layout' => 'vertical'
                        ])
                        ->endRepeater();
                

        return $this->registered_fields;
    }
}      
/**
* Enable the class
 */
new Menukaart_Block();
