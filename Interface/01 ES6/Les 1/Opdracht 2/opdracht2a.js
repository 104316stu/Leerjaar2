const multiply = (a, b) => { 
    return a * b; 
};

const greet = (name) => {
    return `Hello, ` + name + `!`;
};

const double = (num) => {
    return num * 2;
};

const filterEvens = (arr) => arr.filter(num => num % 2 === 0);

console.log(multiply(3, 4))
console.log(greet("Alice"))
console.log(double(5))
console.log(filterEvens([1, 2, 3, 4, 5, 6]))
