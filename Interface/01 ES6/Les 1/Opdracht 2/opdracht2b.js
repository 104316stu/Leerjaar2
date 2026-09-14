let globalVar = 10;

let myObject = {
    waarde: 5,
    regularFunction: function() {
       console.log(this.waarde);
       console.log(globalVar);
    },

    arrowFunction: () => {
       console.log(this.waarde);
       console.log(globalVar);
    }
};

myObject.regularFunction();
myObject.arrowFunction();

// regular = 5, 10
// arrow = undefined, 10
// ze kunnen allebij globalvar zien maar de arrow functie kan de waarde van 'this' niet zien omdat arrow functies geen 'this' hebben