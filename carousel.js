const ROTATE_LEFT = "&laquo;";
const ROTATE_RIGHT = "&raquo;";

const pictures = [
    {src:"/carousel-images/1.jpeg",alt:"Figure 1",href:"/carousel-images/1.jpeg"},
    {src:"/carousel-images/2.jpeg",alt:"Figure 2",href:"/carousel-images/2.jpeg"},
    {src:"/carousel-images/3.jpeg",alt:"Figure 3",href:"/carousel-images/3.jpeg"},
    {src:"/carousel-images/4.jpeg",alt:"Figure 4",href:"/carousel-images/4.jpeg"},
    {src:"/carousel-images/5.jpeg",alt:"Figure 5",href:"/carousel-images/5.jpeg"},
    {src:"/carousel-images/6.jpeg",alt:"Figure 6",href:"/carousel-images/6.jpeg"},
    {src:"/carousel-images/7.jpeg",alt:"Figure 7",href:"/carousel-images/7.jpeg"},
    {src:"/carousel-images/8.jpeg",alt:"Figure 8",href:"/carousel-images/8.jpeg"},
    {src:"/carousel-images/9.jpeg",alt:"Figure 9",href:"/carousel-images/9.jpeg"},
    {src:"/carousel-images/10.jpeg",alt:"Figure 10",href:"/carousel-images/10.jpeg"}
];

const layout = '<a href="LINK"><img src="PATH" alt="TEXT" width="250" height="250" /></a>';

//////////////////////////////////////////////////////////////////////////////////////////////////////
class CarouselButton {
	constructor(ID, text, action) {
		var btn = document.createElement("button");
		btn.innerHTML = text;
		btn.setAttribute("id", ID);
		btn.addEventListener('click', action);
		
		return btn;
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////
class Carousel {

	constructor(wrapper, pictures){
		const carousel = document.getElementById(wrapper);
		carousel.innerHTML = "";

		this.btnRight = new CarouselButton("btnRight", ROTATE_RIGHT, this.rotate);
		this.btnLeft = new CarouselButton("btnLeft", ROTATE_LEFT, this.rotate);

		function prepare(item) {
			var t = layout.replace(/PATH/g,item.src);
			t = t.replace(/TEXT/g,item.alt);			
			return t.replace(/LINK/g,item.href);
			}		
		this.pictures = pictures.map(prepare);

		this.divImages = document.createElement("div");
		
		carousel.appendChild(this.btnLeft);
		carousel.appendChild(this.divImages);
		carousel.appendChild(this.btnRight);
		
		this.show();
	}

// "e" -  ссылочная переменная, указывающая на объект "event", который будет передан обработчику
	rotate = e =>{
		var img = [];
		switch(e.target.id) {			
			case "btnLeft":
				img = this.pictures.shift();
				this.pictures.push(img);		
				break;
			case "btnRight":
				img = this.pictures.pop();
				this.pictures.unshift(img);
				break;					
		}
		this.show();
	}
	
	/* или отдельные обработчики для каждой кнопки*/
	rotateLeft = e =>{
		var img = this.pictures.shift();
		this.pictures.push(img);		
		this.show();
	}

	rotateRight = e =>{
		var img = this.pictures.pop();
		this.pictures.unshift(img);		
		this.show();
	}

	show(){
		this.divImages.innerHTML = (Array.from(this.pictures).splice(3,4)).toString().replace(/,/g,"");
	}
}

const c = new Carousel("jsCarousel", pictures);


////////////////////////////////////////////////////////////


