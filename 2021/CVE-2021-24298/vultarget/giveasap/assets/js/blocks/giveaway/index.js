const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
import logo from '../logo';
import GiveawayEdit from './edit';

registerBlockType( 'simple-giveaways/giveaway', {
    title:  __( 'Giveaway' ),
    description: __( 'Show a giveaway' ),
    icon: logo,
    category: 'simple-giveaways',
    attributes: {
        id: {
            type: 'string',
            default: '0'
        },
        hide_link: {
            type: 'string',
            default: 'current'
        },
        title: {
            type: 'string',
            default: '0'
        },
        prizes: {
            type: 'string',
            default: '1'
        },
        show_total_entries: {
            type: 'string',
            default: '0'
        },
        show_entries: {
            type: 'string',
            default: '0'
        },
        countdown_in_header: {
            type: 'string',
            default: '0'
        },
        hide_winners_number: {
            type: 'string',
            default: '0'
        },
        hide_prize_value: {
            type: 'string',
            default: '0'
        },
        hide_prize_title: {
            type: 'string',
            default: '0'
        },
        content: {
            type: 'string',
            default: '0'
        },
        rules: {
            type: 'string',
            default: '0'
        }
    },
    edit: GiveawayEdit,
    save() {
        return null;
    }
});