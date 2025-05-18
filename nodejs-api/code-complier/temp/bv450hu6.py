def reverse(x: int) -> int:
    sign = -1 if x < 0 else 1
    x = abs(x)
    res = 0
    while x:
        res = res * 10 + x % 10
        x //= 10
    res *= sign
    return res if -2**31 <= res <= 2**31 - 1 else 0

if __name__ == "__main__":
    x = int(input()) 
    result = reverse(x)
    print(result)