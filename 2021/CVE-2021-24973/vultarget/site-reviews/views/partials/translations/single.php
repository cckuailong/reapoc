<tr class="glsr-string-tr {{ data.class }}">
    <td class="glsr-string-td1 column-primary">
        <button type="button" class="toggle-row">
            <span class="screen-reader-text"><?= _x('Show custom translation', 'admin-text', 'site-reviews'); ?></span>
        </button>
        <div>
            <p>{{ data.s1 }}</p>
        </div>
        <p class="row-actions">
            <span class="delete"><a href="#{{ data.index }}" class="delete" aria-label="<?= esc_attr_x('Delete translation string', 'admin-text', 'site-reviews'); ?>"><?= _x('Delete', 'admin-text', 'site-reviews'); ?></a></span>
        </p>
    </td>
    <td class="glsr-string-td2">
        <div>
            <input type="hidden" name="{{ data.prefix }}[settings][strings][{{ data.index }}][id]" value="{{ data.id }}" data-id>
            <input type="hidden" name="{{ data.prefix }}[settings][strings][{{ data.index }}][s1]" value="{{ data.s1 }}">
            <textarea rows="2" name="{{ data.prefix }}[settings][strings][{{ data.index }}][s2]">{{ data.s2 }}</textarea>
            <span class="description">{{ data.desc }}{{ data.error }}</span>
        </div>
    </td>
</tr>
