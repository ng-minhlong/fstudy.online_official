def twoSum(nums, target):
    map = {}
    for i, num in enumerate(nums):
        complement = target - num
        if complement in map:
            return [map[complement], i]
        map[num] = i
    return []

if __name__ == "__main__":
    #s = input().strip()
    s = list(map(int, input().split())) #cái này là nhập string -> tạo ra chuỗi như [1,2,3,...]
    target = int(input())
    result = twoSum(s, target)
    print(result)