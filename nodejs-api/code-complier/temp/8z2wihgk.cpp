#include <unordered_map>
using namespace std;

class TwoSum {
private:
    unordered_map<int, int> nums;

public:
    TwoSum() {
        // Khởi tạo map rỗng
    }

    void add(int number) {
        nums[number]++;
    }

    bool find(int value) {
        for (const auto& [num, count] : nums) {
            int target = value - num;
            if (nums.count(target)) {
                if (target != num || count > 1) {
                    return true;
                }
            }
        }
        return false;
    }
};
#include <iostream>

int main() {
    TwoSum ts;
    ts.add(1);
    ts.add(3);
    ts.add(5);
    cout << boolalpha << ts.find(4) << endl;  // true (1 + 3)
    cout << boolalpha << ts.find(7) << endl;  // false

    return 0;
}
