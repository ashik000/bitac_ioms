export function clone(obj) {
    return JSON.parse(JSON.stringify(obj))
}
export function formatDuration(sec_num){
    let hours   = Math.floor(sec_num / 3600);
    let minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    let seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return hours+':'+minutes+':'+seconds;
}

export function convertToHM(value){
    let sec = parseInt(value, 10);
    let hours   = Math.floor(sec / 3600);
    let minutes = Math.floor((sec - (hours * 3600)) / 60);
    let seconds = sec - (hours * 3600) - (minutes * 60);
    // add 0 if value < 10; Example: 2 => 02
    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}

    if(hours < 1 && minutes < 1){
        return '00m';
    }else if(hours < 1 && minutes >= 1){
        return minutes+'m';
    }else if(hours >= 1 && minutes >= 1){
        return hours+'h'+' '+minutes+'m';
    }else if(hours >= 1 && minutes < 1){
        return hours+'h';
    }else {
        return hours+'h'+' '+minutes+'m';
    }

}
