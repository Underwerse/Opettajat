function removeMessage() {
	document.getElementsByClassName('msg').remove();
}

document.getElementsByClassName('btn').onclick = setTimeout(removeMessage, 2000);

// document.addEventListener('DOMContentLoaded', msgRemove);
// function msgRemove() {
// 	let msg = document.querySelector('.msg');
// 	node.parentNode.removeChild(msg);
// }

// setTimeout(msgRemove, 2000);