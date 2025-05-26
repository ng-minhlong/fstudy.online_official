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
