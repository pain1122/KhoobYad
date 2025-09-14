/**
 * Invoice Print
 */


window.print();
window.onafterprint = back;

function back() {
    window.history.back();
}