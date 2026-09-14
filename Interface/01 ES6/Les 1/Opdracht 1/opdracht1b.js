let globalVar = 10

const modifyVar = () => {
    let localVar = 5
    let innerVar = 2
    console.log(globalVar, localVar, innerVar);
    
}

console.log(globalVar, localVar, innerVar)