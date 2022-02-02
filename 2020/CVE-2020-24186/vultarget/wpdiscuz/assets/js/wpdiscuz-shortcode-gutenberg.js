(function (blocks, editor, element) {
    /* global wpdObject */
    var el = element.createElement;
    var RichText = editor.RichText;
    var wpdIcon = el('svg',
            {
                width: 24,
                height: 24
            },
            el('path',
                    {
                        d: "M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z",
                        style: {
                            fill: '#1db99a'
                        }
                    }
            ),
            el('path',
                    {
                        d: "M0 0h24v24H0z",
                        style: {
                            fill: 'transparent'
                        }
                    }
            )
            );
    blocks.registerBlockType('wpdiscuz/feedback-shortcode', {
        title: 'Feedback Shortcode',
        icon: wpdIcon,
        category: 'common',
        attributes: {
            content: {
                type: 'html',
                default: '[' + wpdObject.shortcode + ' id="' + Math.random().toString(36).substr(2, 10) + '" question="' + wpdObject.leave_feebdack + '" opened="0"][/' + wpdObject.shortcode + ']'
            }
        },
        edit(props) {
            var content = props.attributes.content;
            function onChangeContent(newContent) {
                props.setAttributes({content: newContent});
            }
            return el(
                    RichText,
                    {
                        onChange: onChangeContent,
                        value: content
                    }
            );
        },
        save: function (props) {
            return props.attributes.content;
        }
    });
}(window.wp.blocks, window.wp.editor, window.wp.element));