class TwoSum:

    def __init__(self):
        self.nums = {}  # Dùng dict để lưu tần suất mỗi số

    def add(self, number: int) -> None:
        # Tăng tần suất của number trong dict
        if number in self.nums:
            self.nums[number] += 1
        else:
            self.nums[number] = 1

    def find(self, value: int) -> bool:
        # Duyệt từng số đã thêm
        for num in self.nums:
            target = value - num
            if target in self.nums:
                # Nếu target khác num, ta có 2 số khác nhau tạo ra tổng
                if target != num:
                    return True
                # Nếu target == num, ta cần ít nhất 2 lần xuất hiện của số đó
                elif self.nums[num] > 1:
                    return True
        return False
