const qcList = {
    container : null,
    ul: null,
    input: null,
    select: null,
    get(){
    	let result = [];
		this.select.find('option:selected').each((i, o) => {
			if(o.value) result.push(o.value);
		});
		return result;
	},
	set(e){
    	this.container = e;
    	this.ul = this.container.parent().find('ul');
    	this.input = this.container.find('input');
    	this.select = this.container.parent().find('select');
	},
	render(e){
    	if(e) this.set(e);
		let data = this.get();
	    this.ul.html('');
	    data.forEach((v, i) => {
			this.ul.append(`<li>${v} <a onclick="qcList.delete($(this), ${i})" class="uk-icon-link uk-margin-small-right" uk-icon="minus-circle"></a></li>`)
		})
	},
	delete(e, i){
		this.set(e.parent().parent());
		this.select.find('option')[i].remove();
		this.render();
	},
	isDuplicate(){
		let data = this.get();
		return data.indexOf(this.input.val()) > 0;
	},
	add(e){
		this.set(e.parent());
		if(this.input.val() && !this.isDuplicate()){
			this.select.append(`<option selected>${this.input.val()}</option>`);
			this.render()
		}

		this.input.val('');
	}
};