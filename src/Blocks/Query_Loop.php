<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

abstract class Query_Loop extends ACF_Group {
	
	public const QUERY_LOOP = 'query_loop';
	public const QUERY_TYPE = 'query_type';
	public const AUTOMATIC  = 'automatic';
	public const MANUAL     = 'manual';
	
	public const AUTOMATIC_GROUP = 'automatic_group';
	public const CATEGORIES      = 'categories';
	
	public const MANUAL_GROUP = 'manual_group';
	public const MANUAL_CARDS = 'manual_cards';
	public const MANUAL_CARD  = 'manual_card';

	/**
	 * Default label for single card on Manual Query
	 * Add/Change in Block clack in order to override
	 */
	protected string $default_manual_card_label = 'Card';

	/**
	 * Default max amount of cards applied to the manual query
	 * Add/Change in Block clack in order to override
	 */
	protected int $max_manual_cards = 4;

	/**
	 * Default min amount of cards applied to the manual query
	 * Add/Change in Block clack in order to override
	 */
	protected int $min_manual_cards = 0;

	/**
	 * Default instructions for cards
	 * Add/Change in Block clack in order to override
	 */
	protected string $instructions = '';
	
	protected string $block_name = '';
	
	public function get_query_loop_group( string $name ): array {
		$this->block_name = $name;
		
		return [
			'key'        => $this->get_field_key( self::QUERY_LOOP, $name ),
			'type'       => 'group',
			'label'      => esc_html__( 'Posts Selection', 'ucsc' ),
			'name'       => self::QUERY_LOOP,
			'sub_fields' => $this->get_sub_fields(),
		];
	}
	
	protected function get_sub_fields(): array {
		return [
			$this->get_query_type_filed(),
			$this->get_automatic_query(),
			$this->get_manual_query(),
		];
	}
	
	protected function get_query_type_filed(): array {
		return [
			'key'           => $this->get_field_key( self::QUERY_TYPE, $this->block_name ),
			'type'          => 'button_group',
            'layout'        => 'vertical',
			'name'          => self::QUERY_TYPE,
			'label'         => esc_html__( 'Curated Content', 'ucsc' ),
			'choices'       => [
				self::AUTOMATIC => esc_html__( 'Pull from category', 'ucsc' ),
				self::MANUAL    => esc_html__( 'Select manually', 'ucsc' ),
			],
			'default_value' => self::AUTOMATIC,
		];
	}
	
	protected function get_automatic_query(): array {
		return [
			'key'               => $this->get_field_key( self::AUTOMATIC_GROUP, $this->block_name ),
			'type'              => 'group',
			'label'             => esc_html__( '', 'ucsc' ),
			'name'              => self::QUERY_LOOP,
			'sub_fields'        => [
				$this->get_categories_field(),
			],
			'conditional_logic' => [
				[
					[
						'field'    => $this->get_field_key( self::QUERY_TYPE, $this->block_name ),
						'operator' => '==',
						'value'    => self::AUTOMATIC,
					],
				],
			],
			'wrapper'           => [
				'class' => 'acf-no-style',
			],
		];
	}
	
	protected function get_categories_field(): array {
		return [
			'key'           => $this->get_field_key( self::CATEGORIES, self::AUTOMATIC_GROUP ),
			'label'         => esc_html__( 'Categories', 'ucsc' ),
			'name'          => self::CATEGORIES,
			'type'          => 'taxonomy',
			'taxonomy'      => 'category',
			'add_term'      => 0,
			'field_type'    => 'select',
			'return_format' => 'id',
			'instructions'  => esc_html__( 'Select the category to query.', 'ucsc' ),
		];
	}

	protected function get_manual_query(): array {
		return [
			'key'               => $this->get_field_key( self::MANUAL_GROUP, $this->block_name ),
			'type'              => 'group',
			'label'             => esc_html__( '', 'ucsc' ),
			'name'              => self::MANUAL_GROUP,
			'sub_fields'        => [
				$this->get_cards(),
			],
			'conditional_logic' => [
				[
					[
						'field'    => $this->get_field_key( self::QUERY_TYPE, $this->block_name ),
						'operator' => '!=',
						'value'    => self::AUTOMATIC,
					],
				],
			],
			'wrapper'           => [
				'class' => 'acf-no-style',
			],
		];
	}
	
	protected function get_cards(): array {
		return [
			'key'               => $this->get_field_key( self::MANUAL_CARDS, self::MANUAL_GROUP ),
			'type'              => 'repeater',
			'name'              => self::MANUAL_CARDS,
			'sub_fields'        => [
				$this->get_manual_card(),
			],
			'button_label'      => sprintf( 'Add %s', $this->default_manual_card_label ),
			'min'               => $this->min_manual_cards,
			'max'               => $this->max_manual_cards,
			'instructions'      => $this->instructions,
			'layout'            => 'block',
			'conditional_logic' => [
				[
					[
						'field'    => $this->get_field_key( self::QUERY_TYPE, $this->block_name ),
						'operator' => '!=',
						'value'    => self::AUTOMATIC,
					],
				],
			],
		];
	}
	
	protected function get_manual_card(): array {
		return [
			'key'           => $this->get_field_key( self::MANUAL_CARD, self::MANUAL_CARDS ),
			'label'         => $this->default_manual_card_label,
			'post_type'     => [
				'post',
			],
			'type'          => 'post_object',
			'name'          => self::MANUAL_CARD,
			'return_format' => 'id',
			'ui'            => 1,
			'required'      => 0,
		];
	}

}