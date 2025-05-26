def roman_to_int(s):
    roman_values = {'I': 1, 'V': 5, 'X': 10, 'L': 50, 'C': 100, 'D': 500, 'M': 1000}
    total = 0
    prev_value = 0
    
    for c in s:
        current_value = roman_values[c]
        if current_value > prev_value:
            total += current_value - 2 * prev_value
        else:
            total += current_value
        prev_value = current_value

    return total

if __name__ == "__main__":
    s = input()
    #s = list(map(int, input().split())) #cái này là nhập string -> tạo ra chuỗi như [1,2,3,...]
    result = roman_to_int(s)
    print(result)