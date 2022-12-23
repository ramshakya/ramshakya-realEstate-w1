function arraysEqual(a1, a2) {
  /* WARNING: arrays must not contain {objects} or behavior may be undefined */
  let flag=true;
  if (Array.isArray(a1) && Array.isArray(a2)) {
    a1.forEach((element) => {
      if (!a2.includes(element)) {
          return flag= false;
        }
    });
    return flag;
  }
}

module.exports = {
  arraysEqual,
};
