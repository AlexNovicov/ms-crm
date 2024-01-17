/**
 * Get select options.
 *
 * @param {Immutable.List} options
 * @return {object[]}
 */
export function getSelectOptions(options) {

    if(!options) {
        return [];
    }

    let result = [];

    options.forEach((option, key) => {
        result.push({label: option, value: key});
    });

    return result;
}

/**
 * Get grouped select options.
 *
 * @param {Immutable.List} grouped_options
 * @return {object[]}
 */
export function getGroupedSelectOptions(grouped_options) {

    if(!grouped_options) {
        return [];
    }

    let result = [];

    grouped_options.forEach((options, group_key) => {
        result.push({label: group_key, options: getSelectOptions(options)});
    });

    return result;
}

/**
 * Get selected value for select component.
 *
 * @param {string} selected_value
 * @param {object[]} select_options
 * @return {object|null}
 */
export function getSelectedValue(selected_value, select_options) {

    let result;

    select_options.forEach((option) => {
        if(option.options) {
            option.options.forEach((option2) => {
                if(''+option2.value === ''+selected_value) {
                    result = {label: option2.label, value: option2.value};
                }
            });
        } else if(''+option.value === ''+selected_value) {
            result = {label: option.label, value: option.value};
        }
    });

    return result || null;
}
