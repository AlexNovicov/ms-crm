/**
 * Serialize form.
 *
 * @param form
 * @returns {{}}
 */
export function serializeForm(form) {

    let serialized = {},
        array_indexes = {};

    for (let i = 0; i < form.elements.length; i++) {

        const field = form.elements[i];

        // Don't serialize fields without a name, submits, buttons, file and reset inputs, and disabled fields
        if (!field.name || field.disabled || field.type === 'file' || field.type === 'reset' || field.type === 'submit' || field.type === 'button') continue;

        // If a multi-select, get all selections
        if (field.type === 'select-multiple') {
            for (let n = 0; n < field.options.length; n++) {
                if (!field.options[n].selected) continue;
                serialized[field.name] = field.options[n].value;
            }
        }

        // Convert field data to a query string
        else if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
            if(field.name.indexOf('[]') !== -1) {
                let name_without_brackets = field.name.replace('[]', '');
                if(!array_indexes[name_without_brackets]) array_indexes[name_without_brackets] = 0;
                serialized[`${name_without_brackets}[${array_indexes[name_without_brackets]++}]`] = field.value;
            } else {
                serialized[field.name] = field.value;
            }
        }
    }

    return serialized;
}
