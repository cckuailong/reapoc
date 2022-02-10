'use strict';
function wot_sort_carriers(data) {
    let n = data.length;
    for (let i = 0; i < n - 1; i++) {
        let check = false;
        for (let j = i + 1; j < n; j++) {
            if (data[i].name.toLowerCase() > data[j].name.toLowerCase()) {
                let tmp = data[i];
                data[i] = data[j];
                data[j] = tmp;
                check = true;
            }
        }
        if (!check) {
            break;
        }
    }
    return data;
}