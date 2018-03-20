// taken from https://stackoverflow.com/a/1497512/5394093
function generatePassword(length) {
  length = (typeof length !== 'undefined') ?  length : 12;
  charset = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789--__##$$!!..";
  // slightly modified charset to avoid confusable characters and add a few special characters
  retVal = "";
  for (var i = 0, n = charset.length; i < length; ++i) {
    retVal += charset.charAt(Math.floor(Math.random() * n));
  }
  return retVal;
}
