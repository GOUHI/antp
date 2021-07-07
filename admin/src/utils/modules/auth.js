export function getCurrentAuthority(){
  return ["user"]
}

export function check(authoruty){
  const current = getCurrentAuthority()
  return current.some(item=>authoruty.includes(item))
}

export function isLogin(){
  const current = getCurrentAuthority()
  return current && current[0] !== "guest"
}